<?php

namespace site\modules\users\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use app\helpers\RouteHelper;
use yii\tools\traits\AjaxValidationTrait;
use app\modules\site\components\Controller;
use site\modules\design\interfaces\ContextInterface;
use site\modules\users\Finder;
use site\modules\users\models\RegistrationForm;
use site\modules\users\models\ResendForm;
use site\modules\users\models\User;

/**
 * RegistrationController is responsible for all registration process, which includes registration of a new account,
 * resending confirmation tokens, email confirmation and registration via social networks.
 *
 * @property \site\modules\users\Module $module
 */
class RegistrationController extends Controller
{
    use AjaxValidationTrait;

    /** @var Finder */
    protected $finder;

    /**
     * @inheritdoc
     */
    public function __construct($id, $module, ContextInterface $designContext, Finder $finder, $config = [])
    {
        $this->finder = $finder;
        parent::__construct($id, $module, $designContext, $config);
    }

    /** @inheritdoc */
    public function behaviors()
    {
        return array_replace_recursive(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['connect'],
                        'roles' => ['?']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['register', 'confirm', 'resend'],
                        'roles' => ['?', '@']
                    ],
                ],
            ],
        ]);
    }

    /**
     * Displays the registration page.
     * After successful registration if enableConfirmation is enabled
     * shows info message otherwise redirects to home page.
     *
     * @return string
     * @throws \yii\web\HttpException
     */
    public function actionRegister()
    {
        if (!$this->module->enableRegistration) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        if (!Yii::$app->getUser()->isGuest) {
            return $this->redirect([RouteHelper::SITE_USERS_SETTINGS_PROFILE]);
        }

        /** @var RegistrationForm $model */
        $model = Yii::createObject(RegistrationForm::className());

        if ($model->load(Yii::$app->getRequest()->post())) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->end(0, $this->performAjaxValidation($model));
            }
            $result = $model->register();
            Yii::$container->set(RegistrationForm::className(), $model);
            if ($result) {
                return $this->redirect([RouteHelper::SITE_USERS_SETTINGS_PROFILE]);
            }
        }

        return $this->render('register');
    }

    /**
     * Displays page where user can create new account that will be connected to social account.
     *
     * @param string $code
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionConnect($code)
    {
        $account = $this->finder->findAccount()->byCode($code)->one();

        if ($account === null || $account->getIsConnected()) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        /** @var User $user */
        $user = Yii::createObject([
            'class'    => User::className(),
            'scenario' => 'connect',
            'username' => $account->username,
            'email'    => $account->email,
        ]);

        if ($user->load(Yii::$app->request->post()) && $user->create()) {
            $account->connect($user);
            Yii::$app->user->login($user, $this->module->rememberFor);
            return $this->goBack();
        }

        return $this->render('connect', [
            'model'   => $user,
            'account' => $account,
        ]);
    }

    /**
     * Confirms user's account. If confirmation was successful logs the user and shows success message. Otherwise
     * shows error message.
     *
     * @param int    $id
     * @param string $code
     *
     * @return string
     * @throws \yii\web\HttpException
     */
    public function actionConfirm($key)
    {
        parse_str(base64_decode($key));
        Yii::info('Token data received and parsed.' . PHP_EOL . 'id=' . $id . ', code=' . $code, __METHOD__);

        $user = $this->finder->findUserById($id);

        if ($user === null || $this->module->enableConfirmation == false) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        $this->redirect($user->attemptConfirmation($code)
                ? [RouteHelper::SITE_USERS_SETTINGS_PROFILE]
                : [RouteHelper::SITE_USERS_REGISTRATION_RESEND]);
    }

    /**
     * Displays page where user can request new confirmation token. If resending was successful, displays message.
     *
     * @return string
     * @throws \yii\web\HttpException
     */
    public function actionResend()
    {
        if ($this->module->enableConfirmation == false) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        /** @var ResendForm $model */
        $model = Yii::createObject(ResendForm::className());

        if ($model->load(Yii::$app->request->post())) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->end(0, $this->performAjaxValidation($model));
            }
            $result = $model->resend();
            Yii::$container->set(ResendForm::className(), $model);
            if ($result) {
                return $this->redirect([RouteHelper::SITE_USERS_LOGIN]);
            }
        }

        return $this->render('resend');
    }
}
