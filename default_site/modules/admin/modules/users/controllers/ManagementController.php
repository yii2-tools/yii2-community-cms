<?php

namespace admin\modules\users\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use app\helpers\ModuleHelper;
use yii\tools\crud\Action as BaseCrudAction;
use app\modules\admin\components\Controller;
use yii\tools\traits\AjaxValidationTrait;
use site\modules\users\Finder;
use site\modules\users\models\User;
use site\modules\users\models\Profile;
use admin\modules\users\models\UserSearch;

class ManagementController extends Controller
{
    use AjaxValidationTrait;

    /** @var Finder */
    protected $finder;

    /**
     * @param string  $id
     * @param \yii\base\Module $module
     * @param Finder  $finder
     * @param array   $config
     */
    public function __construct($id, $module, Finder $finder, $config = [])
    {
        $this->finder = $finder;
        parent::__construct($id, $module, $config);
    }

    /** @inheritdoc */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete'  => ['post'],
                    'block'  => ['post'],
                    'confirm'  => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'index' => [
                'class' => 'yii\tools\crud\ReadAction',
                'modelPolicy' => BaseCrudAction::MODEL_POLICY_NONE,
                'beforeCallback' => function ($action) {
                    $action->params['searchModel'] = Yii::createObject(UserSearch::className());
                    $action->params['dataProvider'] = $action->params['searchModel']->search(Yii::$app->request->get());
                }
            ],
            'create' => [
                'class' => 'yii\tools\crud\CreateAction',
                'model' => User::className(),
                'modelAction' => 'create',
            ],
            'update' => [
                'class' => 'yii\tools\crud\UpdateAction',
                'view' => '_account',
                'model' => User::className(),
            ],
            'update-profile' => [
                'class' => 'yii\tools\crud\UpdateAction',
                'view' => '_profile',
                'model' => Profile::className(),
                'searchKey' => 'user_id',
                'requestKey' => 'id',
            ],
            'info' => [
                'class' => 'yii\tools\crud\ReadAction',
                'view' => '_info',
                'model' => User::className(),
            ],
            'assignments' => [
                'class' => 'yii\tools\crud\ReadAction',
                'view' => '_assignments',
                'model' => User::className(),
            ],
            'confirm' => [
                'class' => 'yii\tools\components\Action',
                'beforeCallback' => function ($action) {
                    $modelId = Yii::$app->request->get('id');
                    $action->controller->findModel($modelId)->confirm();
                    Yii::$app->getSession()->setFlash('success', Yii::t(ModuleHelper::USERS, 'User has been confirmed'));
                    $action->response = $action->controller->redirect(Url::previous('actions-redirect'));
                },
            ],
            'delete' => [
                'class' => 'yii\tools\crud\DeleteAction',
                'model' => User::className(),
                'beforeCrudAction' => function ($event) {
                    $userId = Yii::$app->user->getId();
                    if (in_array($event->action->model->id, [$userId, 1])) {
                        $event->action->response = $event->action->controller
                            ->redirect(Url::previous('actions-redirect'));
                        Yii::$app->getSession()->setFlash(
                            'danger',
                            $event->action->model->id == $userId
                                ? Yii::t(ModuleHelper::USERS, 'You can not remove your own account')
                                : Yii::t(ModuleHelper::USERS, 'You can not remove super admin account')
                        );
                        $event->isValid = false;
                    }
                },
                'afterCrudAction' => function ($event) {
                    Yii::$app->getAuthManager()->revokeAll($event->action->model->id);
                },
                'flashSuccess' => Yii::t(ModuleHelper::USERS, 'User has been deleted')
            ],
            'block' => [
                'class' => 'yii\tools\components\Action',
                'beforeCallback' => function ($action) {
                    $action->response = $action->controller->redirect(Url::previous('actions-redirect'));
                    $modelId = Yii::$app->request->get('id');
                    $userId = Yii::$app->user->getId();
                    if (in_array($modelId, [$userId, 1])) {
                        Yii::$app->getSession()->setFlash(
                            'danger',
                            $modelId == $userId
                                ? Yii::t(ModuleHelper::USERS, 'You can not block your own account')
                                : Yii::t(ModuleHelper::USERS, 'You can not block super admin account')
                        );

                        return;
                    }
                    $model = $action->controller->findModel($modelId);
                    if ($model->getIsBlocked()) {
                        $model->unblock();
                        Yii::$app->getSession()->setFlash('success', Yii::t(ModuleHelper::USERS, 'User has been unblocked'));
                    } else {
                        $model->block();
                        Yii::$app->getSession()->setFlash('success', Yii::t(ModuleHelper::USERS, 'User has been blocked'));
                    }
                },
            ],
        ];
    }

    public function afterAction($action, $result)
    {
        Url::remember('', 'actions-redirect');

        return parent::afterAction($action, $result);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param int $id
     *
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $user = $this->finder->findUserById($id);

        if ($user === null) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        return $user;
    }
}
