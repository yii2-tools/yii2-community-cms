<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 08.05.2016 00:12
 * via Gii Module Generator
 */

namespace site\modules\news\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\data\Pagination;
use yii\web\ForbiddenHttpException;
use yii\tools\crud\Action as BaseCrudAction;
use app\modules\site\components\Controller;
use site\modules\news\Module;
use site\modules\news\Finder;
use site\modules\news\assets\NewsAsset;
use site\modules\news\models\NewsForm;
use admin\modules\users\helpers\RbacHelper;

class DefaultController extends Controller
{
    /**
     * @var string
     */
    public $modelClass = 'site\modules\news\models\NewsRecord';

    /**
     * @var Finder
     */
    public $finder;

    /**
     * @inheritdoc
     */
    public function __construct($id, $module, Finder $finder, $config = [])
    {
        $this->finder = $finder;
        parent::__construct($id, $module, $config);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        NewsAsset::register($this->getView());
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['create'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['?', '@'],
                        'matchCallback' => function () {
                            $user = Yii::$app->getUser();

                            return $user->can(RbacHelper::NEWS_ADD)
                                || (($identity = $user->getIdentity()) && $identity->isAdmin);
                        },
                    ],
                ],
            ],
        ]);
    }

    /**
     * @return array
     */
    public function actions()
    {
        return array_merge(parent::actions(), [
            'index' => [
                'class' => 'yii\tools\crud\ReadAction',
                'modelPolicy' => BaseCrudAction::MODEL_POLICY_NONE,
                'beforeCallback' => function ($action) {
                    Yii::$container->set(Pagination::className(), [
                        'totalCount' => $this->module->params['news_count'],
                        'defaultPageSize' => Module::NEWS_PER_PAGE,
                    ]);

                    $action->params['news'] = $this->finder->findNews([], true, true, true);
                }
            ],
            'show' => [
                'class' => 'yii\tools\crud\ReadAction',
                'model' => $this->modelClass,
                'beforeRenderCallback' => function ($action) {
                    $action->params['newsRecord'] = $action->model;
                },
            ],
            'create' => [
                'class' => 'yii\tools\crud\CreateAction',
                'model' => $this->modelClass,
                'beforeRenderCallback' => function ($action) {
                    Yii::$container->set(NewsForm::className(), [
                        'model' => $action->model,
                    ]);
                },
                'redirectSuccess' => ['index'],
            ],
            'update' => [
                'class' => 'yii\tools\crud\UpdateAction',
                'model' => $this->modelClass,
                'beforeCallback' => function ($action) {
                    $user = Yii::$app->getUser();

                    if (!$user->can(RbacHelper::NEWS_UPDATE, ['entity' => $action->model])
                        && (!($identity = $user->getIdentity()) || !$identity->isAdmin)
                    ) {
                        throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
                    }
                },
                'beforeRenderCallback' => function ($action) {
                    $action->params['newsRecord'] = $action->model;
                    Yii::$container->set(NewsForm::className(), [
                        'model' => $action->model,
                    ]);
                },
            ],
            'delete' => [
                'class' => 'yii\tools\crud\DeleteAction',
                'model' => $this->modelClass,
                'beforeCallback' => function ($action) {
                    $user = Yii::$app->getUser();

                    if (!$user->can(RbacHelper::NEWS_DELETE, ['entity' => $action->model])
                        && (!($identity = $user->getIdentity()) || !$identity->isAdmin)
                    ) {
                        throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
                    }
                },
                'redirectSuccess' => ['index'],
            ]
        ]);
    }
}
