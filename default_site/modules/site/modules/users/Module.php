<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 23.01.16 17:56
 */

namespace site\modules\users;

use Yii;
use yii\helpers\ArrayHelper;
use yii\base\BootstrapInterface;
use yii\base\Event;
use yii\web\Application;
use yii\authclient\Collection;
use app\helpers\ModuleHelper;
use app\helpers\RouteHelper;
use app\helpers\SessionHelper;
use app\modules\site\components\Module as BaseModule;
use yii\tools\params\models\ActiveParam;
use site\modules\users\interfaces\ObserverInterface;
use site\modules\users\models\User;
use site\modules\users\models\Profile;
use site\modules\users\models\SocialAccount;
use site\modules\users\models\Token;
use site\modules\users\models\LoginForm;
use site\modules\users\events\AuthAttemptEvent;

class Module extends BaseModule implements BootstrapInterface
{
    /** Email is changed right after user enter's new email address. */
    const STRATEGY_INSECURE = 0;

    /** Email is changed after user clicks confirmation link sent to his new email address. */
    const STRATEGY_DEFAULT = 1;

    /** Email is changed after user clicks both confirmation links sent to his old and new email addresses. */
    const STRATEGY_SECURE = 2;

    /** @var bool Whether to enable registration. */
    public $enableRegistration = true;

    /** @var bool Whether to remove password field from registration form. */
    public $enableGeneratingPassword = false;

    /** @var bool Whether user has to confirm his account. */
    public $enableConfirmation = true;

    /** @var bool Whether to allow logging in without confirmation. */
    public $enableUnconfirmedLogin = false;

    /** @var bool Whether to enable password recovery. */
    public $enablePasswordRecovery = true;

    /** @var int Email changing strategy. */
    public $emailChangeStrategy = self::STRATEGY_SECURE;

    /** @var int The time you want the user will be remembered without asking for credentials. */
    public $rememberFor = 604800; // one week

    /** @var int The time before a confirmation token becomes invalid. */
    public $confirmWithin = 86400; // 24 hours

    /** @var int The time before a recovery token becomes invalid. */
    public $recoverWithin = 21600; // 6 hours

    /** @var int Cost parameter used by the Blowfish hash algorithm. */
    public $cost = 10;

    /** @var int */
    public $authTimeout = 28800; // 8h

    /** @var array Mailer configuration */
    public $mailer = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        Yii::$container->setSingleton(Finder::className(), [
            'userQuery'    => User::find(),
            'profileQuery' => Profile::find(),
            'tokenQuery'   => Token::find(),
            'accountQuery' => SocialAccount::find(),
        ]);

        Yii::$container->set('yii\web\User', [
            'identityClass'   => User::className(),
            'enableAutoLogin' => true,
            'authTimeout'     => $this->authTimeout,
            'loginUrl'        => [RouteHelper::SITE_USERS_LOGIN],
        ]);

        Yii::$container->setSingleton('site\modules\users\interfaces\ObserverInterface', $this->getObserver());

        Yii::$app->set('authClientCollection', [
            'class' => Collection::className(),
        ]);

        Yii::$container->set('site\modules\users\Mailer', $this->mailer);

        Event::on(LoginForm::className(), LoginForm::EVENT_ATTEMPT_LOGIN, [$this, 'onAuthAttempt']);

        Yii::$app->on(Application::EVENT_BEFORE_ACTION, [$this, 'assignGuestRole']);
        Yii::$app->on(Application::EVENT_BEFORE_ACTION, [$this, 'ensureActiveParamCookies']);
        Yii::$app->on(Application::EVENT_BEFORE_ACTION, [$this, 'registerActivity']);
    }

    /**
     * @return ObserverInterface
     */
    public function getObserver()
    {
        return $this->get('observer');
    }

    /**
     * Used in login/register actions for supporting captcha protection
     * @param $event
     */
    public function onAuthAttempt(AuthAttemptEvent $event)
    {
        Yii::$app->getSession()->set(SessionHelper::AUTH_LOGIN, ArrayHelper::getValue($event, 'login', ''));

        $authAttemptsCurrent = Yii::$app->session->get(SessionHelper::AUTH_ATTEMPTS);
        $authAttemptsNew = $authAttemptsCurrent + 1;
        $this->setAuthAttempts($authAttemptsNew);

        if ($authAttemptsNew % (User::MAX_AUTH_ATTEMPTS_COUNT + 1) == 0) {
            Yii::$app->getModule(ModuleHelper::SITE)->captcha->enableBy($this, 'too many auth attempts');
        }
    }

    /**
     * Actions after successful authorization of current user.
     */
    public function onAuthSuccess()
    {
        $this->setAuthAttempts(0);
    }

    /**
     * @param int $authAttempts
     */
    public function setAuthAttempts($authAttempts = 0)
    {
        Yii::$app->session->set(SessionHelper::AUTH_ATTEMPTS, $authAttempts);
    }

    /**
     * Give current user base permissions (available for each application user).
     */
    public function assignGuestRole()
    {
        if (empty($this->params['guest_role'])) {
            Yii::warning('Guest role is not defined or invalid, probably some kind of integrity error', __METHOD__);

            return;
        }

        Yii::$app->getAuthManager()->defaultRoles[] = $this->params['guest_role'];
        Yii::info('Guest role: ' . $this->params['guest_role'], __METHOD__);
    }

    /**
     * Updates current user params which based on cookie values.
     */
    public function ensureActiveParamCookies()
    {
        if ($identity = Yii::$app->getUser()->getIdentity()) {
            foreach ($identity->params as $param) {
                if ($param->flags & ActiveParam::COOKIE && isset($_COOKIE[$param->name])) {
                    $param->set($_COOKIE[$param->name]);
                }
            }
        }
    }

    /**
     * Registers activity of current user within application instance.
     */
    public function registerActivity()
    {
        if ($identity = Yii::$app->getUser()->getIdentity()) {
            $identity->touch('activity_at');
        }

        $this->getObserver()->updateOnlineData($identity);
    }
}
