<?php

namespace site\modules\users\controllers;

use Yii;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\helpers\ModuleHelper;
use app\helpers\RouteHelper;
use app\models\UploadForm;
use yii\tools\traits\AjaxValidationTrait;
use app\modules\site\components\Controller;
use site\modules\design\interfaces\ContextInterface;
use site\modules\users\Module;
use site\modules\users\Finder;
use site\modules\users\models\SettingsForm;

/**
 * SettingsController manages updating user settings (e.g. profile, email and password).
 */
class SettingsController extends Controller
{
    use AjaxValidationTrait;

    /** @inheritdoc */
    public $defaultAction = 'profile';

    /** @var Finder */
    protected $finder;

    /**
     * @param string           $id
     * @param \yii\base\Module $module
     * @param Finder           $finder
     * @param array            $config
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
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'disconnect' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow'   => true,
                        'actions' => ['profile', 'account', 'networks', 'disconnect', 'profile-image-upload'],
                        'roles'   => ['@'],
                    ],
                    [
                        'allow'   => true,
                        'actions' => ['confirm'],
                        'roles'   => ['?', '@'],
                    ],
                ],
            ],
        ]);
    }

    /**
     * Shows profile settings form.
     *
     * @return string|\yii\web\Response
     */
    public function actionProfile()
    {
        $model = $this->finder->findProfileById(Yii::$app->user->identity->getId());

        if ($model->load(Yii::$app->request->post())) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->end(0, $this->performAjaxValidation($model));
            }
            if (!empty($_FILES) && $uploadForm = $this->uploadImage($model->user_id)) {
                $model->image_url = $uploadForm->uploadedUrl();
            }
            if ($model->save()) {
                Yii::$app->getSession()->setFlash('success', Yii::t(ModuleHelper::USERS, 'Your profile has been updated'));
                return $this->refresh();
            }
        }

        return $this->render('profile');
    }

    /**
     * Displays page where user can update account settings (username, email or password).
     *
     * @return string|\yii\web\Response
     */
    public function actionAccount()
    {
        /** @var SettingsForm $model */
        $model = Yii::createObject(SettingsForm::className());

        if ($model->load(Yii::$app->request->post())) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->end(0, $this->performAjaxValidation($model));
            }
            $result = $model->save();
            Yii::$container->set(SettingsForm::className(), $model);
            if ($result) {
                Yii::$app->session->setFlash('success', Yii::t(ModuleHelper::USERS, 'Your account details have been updated'));

                return $this->refresh();
            }
        }

        return $this->render('account');
    }

    /**
     * Attempts changing user's password.
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

        if ($user === null || $this->module->emailChangeStrategy == Module::STRATEGY_INSECURE) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        $user->attemptEmailChange($code);

        return $this->redirect(Yii::$app->user->id == $id
                ? [RouteHelper::SITE_USERS_SETTINGS_ACCOUNT]
                : [RouteHelper::SITE_USERS_LOGIN]);
    }

    /**
     * Displays list of connected network accounts.
     *
     * @return string
     */
    public function actionNetworks()
    {
        return $this->render('networks', [
            'user' => Yii::$app->user->identity,
        ]);
    }

    /**
     * Disconnects a network account from user.
     *
     * @param int $id
     *
     * @return \yii\web\Response
     * @throws \yii\web\NotFoundHttpException
     * @throws \yii\web\ForbiddenHttpException
     */
    public function actionDisconnect($id)
    {
        $account = $this->finder->findAccount()->byId($id)->one();

        if ($account === null) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }
        if ($account->user_id != Yii::$app->user->id) {
            throw new ForbiddenHttpException();
        }
        $account->delete();

        return $this->redirect(['networks']);
    }

    public function actionProfileImageUpload($id = null)
    {
        $result = 0;

        if (Yii::$app->request->isPost) {
            $profileModel = $this->finder->findProfileById($id);
            if ($profileModel != null && $profileModel->user_id == Yii::$app->user->identity->getId()) {
                if ($uploadForm = $this->uploadImage($id)) {
                    $profileModel->image_url = $uploadForm->uploadedUrl();
                    if ($profileModel->save()) {
                        $result = 1;
                    }
                }
            }
        }

        \Yii::$app->response->format = Response::FORMAT_JSON;
        \Yii::$app->response->data = [
            'status' => $result,
            'errors' => !empty($uploadForm) ? $uploadForm->getErrors() : null,
            'data' => [
                'image_url' => $result ? $profileModel->image_url : ''
            ]
        ];
        \Yii::$app->end();
    }

    /**
     * @param $userId
     * @return bool
     */
    protected function uploadImage($userId)
    {
        $uploadForm = (new UploadForm())->addSegments(['user', $userId]);
        $uploadForm->imageFile = UploadedFile::getInstance($uploadForm, 'imageFile');

        if ($uploadForm->upload()) {
            return $uploadForm;
        }

        return null;
    }
}
