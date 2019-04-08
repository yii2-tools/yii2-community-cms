<?php

namespace site\modules\users\models;

use Yii;
use yii\base\Model;
use app\helpers\ModuleHelper;
use app\helpers\RouteHelper;
use site\components\validators\CaptchaValidator;
use site\modules\users\Finder;
use site\modules\users\helpers\Password;
use site\modules\users\events\AuthAttemptEvent;

class LoginForm extends Model
{
    /** @var string User's email or username */
    public $login;

    /** @var string User's plain password */
    public $password;

    /** @var string Whether to remember the user */
    public $rememberMe = false;

    public $captcha;

    /** @var \site\modules\users\models\User */
    protected $user;

    /** @var \site\modules\users\Module */
    protected $module;

    /** @var Finder */
    protected $finder;

    const EVENT_ATTEMPT_LOGIN = 'attemptLogin';

    /**
     * @param Finder $finder
     * @param array  $config
     */
    public function __construct(Finder $finder, $config = [])
    {
        $this->finder = $finder;
        $this->module = Yii::$app->getModule(ModuleHelper::USERS);
        parent::__construct($config);
    }

    /** @inheritdoc */
    public function attributeLabels()
    {
        return [
            'login'      => Yii::t(ModuleHelper::USERS, 'Login'),
            'password'   => Yii::t(ModuleHelper::USERS, 'Password'),
            'rememberMe' => Yii::t(ModuleHelper::USERS, 'Remember me next time'),
            'captcha' => Yii::t(ModuleHelper::SITE, 'Captcha'),
        ];
    }

    /** @inheritdoc */
    public function rules()
    {
        return [
            'requiredFields' => [['login', 'password'], 'required'],
            'loginTrim' => ['login', 'trim'],
            'passwordValidate' => [
                'password',
                function ($attribute) {
                    if ($this->user === null || !Password::validate($this->password, $this->user->password_hash)) {
                        $this->addError($attribute, Yii::t(ModuleHelper::USERS, 'Invalid login or password'));
                    }
                }
            ],
            'confirmationValidate' => [
                'login',
                function ($attribute) {
                    if ($this->user !== null) {
                        $confirmationRequired = $this->module->enableConfirmation
                            && !$this->module->enableUnconfirmedLogin;
                        if ($confirmationRequired && !$this->user->getIsConfirmed()) {
                            $this->addError($attribute, Yii::t(ModuleHelper::USERS, 'You need to confirm your email address'));
                        }
                        if ($this->user->getIsBlocked()) {
                            $this->addError($attribute, Yii::t(ModuleHelper::USERS, 'Your account has been blocked'));
                        }
                    }
                }
            ],
            'rememberMe' => ['rememberMe', 'boolean'],
            'captchaRequired' => ['captcha', 'required', 'when' => function ($model) {
                $captcha = Yii::$app->getModule(ModuleHelper::SITE)->captcha;
                return $captcha->isRequired() && $captcha->isExpired();
            }],
            'captchaValidation' => ['captcha', CaptchaValidator::className(), 'when' => function ($model) {
                $captcha = Yii::$app->getModule(ModuleHelper::SITE)->captcha;
                return $captcha->isRequired() && $captcha->isExpired();
            }, 'captchaAction' => RouteHelper::SITE_CAPTCHA],
        ];
    }

    /**
     * Validates form and logs the user in.
     *
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        return $this->validate()
            ? Yii::$app->getUser()->login($this->user, $this->rememberMe ? $this->module->rememberFor : 0)
            : false;
    }

    /** @inheritdoc */
    public function formName()
    {
        return 'login-form';
    }

    /** @inheritdoc */
    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            $this->trigger(self::EVENT_ATTEMPT_LOGIN, new AuthAttemptEvent(['login' => $this->login]));
            $this->user = $this->finder->findUserByUsernameOrEmail($this->login);

            return true;
        }

        return false;
    }
}
