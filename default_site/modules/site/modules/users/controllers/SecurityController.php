<?php

namespace site\modules\users\controllers;

use Yii;
use yii\authclient\AuthAction;
use yii\authclient\ClientInterface;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use app\helpers\ModuleHelper;
use app\helpers\RouteHelper;
use yii\tools\traits\AjaxValidationTrait;
use app\modules\site\components\Controller;
use site\modules\design\interfaces\ContextInterface;
use site\modules\users\Finder;
use site\modules\users\models\LoginForm;
use site\modules\users\models\User;
use site\modules\users\models\SocialAccount;

class SecurityController extends Controller
{
    use AjaxValidationTrait;

    /** @var Finder */
    protected $finder;

    /**
     * @inheritdoc
     */
    public function __construct($id, $module, Finder $finder, ContextInterface $designContext, $config = [])
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
                    ['allow' => true, 'actions' => ['login', 'auth', 'blocked'], 'roles' => ['?']],
                    ['allow' => true, 'actions' => ['login', 'auth', 'logout'], 'roles' => ['@']],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ]);
    }

    /** @inheritdoc */
    public function actions()
    {
        return [
            'auth' => [
                'class' => AuthAction::className(),
                // if user is not logged in, will try to log him in, otherwise
                // will try to connect social account to user.
                'successCallback' => Yii::$app->user->isGuest
                        ? [$this, 'authenticate']
                        : [$this, 'connect'],
            ],
        ];
    }

    /**
     * Displays the login page.
     *
     * @return string|Response
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect([RouteHelper::SITE_USERS_SETTINGS_PROFILE]);
        }

        /** @var LoginForm $model */
        $model = Yii::createObject(LoginForm::className());

        if ($model->load(Yii::$app->getRequest()->post())) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->end(0, $this->performAjaxValidation($model));
            }
            $result = $model->login();
            Yii::$container->set(LoginForm::className(), $model);
            if ($result) {
                $profile = Yii::$app->user->identity->profile;
                if (empty($profile->name) || empty($profile->location)) {
                    Yii::$app->getSession()->setFlash('info', Yii::t(ModuleHelper::USERS, 'Please complete filling profile info'));
                    return $this->redirect([RouteHelper::SITE_USERS_SETTINGS_PROFILE]);
                }
                return $this->goBack();
            }
        }

        return $this->render('login');
    }

    /**
     * Logs the user out and then redirects to the homepage.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->getUser()->logout();

        return $this->goHome();
    }

    /**
     * Tries to authenticate user via social network. If user has already used
     * this network's account, he will be logged in. Otherwise, it will try
     * to create new user account.
     *
     * @param ClientInterface $client
     */
    public function authenticate(ClientInterface $client)
    {
        $account = $this->finder->findSocialAccount()->byClient($client)->one();

        if ($account === null) {
            $account = SocialAccount::create($client);
        }

        if ($account->user instanceof User) {
            if ($account->user->isBlocked) {
                Yii::$app->session->setFlash('danger', Yii::t(ModuleHelper::USERS, 'Your account has been blocked.'));
                $this->action->successUrl = Url::to([RouteHelper::SITE_USERS_LOGIN]);

                return;
            }

            Yii::$app->user->login($account->user, $this->module->rememberFor);
            $this->action->successUrl = Yii::$app->getUser()->getReturnUrl();

            return;
        }

        $this->action->successUrl = $account->getConnectUrl();
    }

    /**
     * Tries to connect social account to user.
     *
     * @param ClientInterface $client
     */
    public function connect(ClientInterface $client)
    {
        /** @var SocialAccount $account */
        $account = Yii::createObject(SocialAccount::className());
        $account->connectWithUser($client);
        $this->action->successUrl = Url::to([RouteHelper::SITE_USERS_SETTINGS_NETWORKS]);
    }
}
