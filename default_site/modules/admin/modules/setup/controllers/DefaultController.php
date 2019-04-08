<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 08.02.2016 14:19
 * via Gii Module Generator
 */

namespace admin\modules\setup\controllers;

use Yii;
use yii\filters\VerbFilter;
use app\helpers\ModuleHelper;
use app\helpers\RouteHelper;
use app\modules\admin\components\Controller;
use yii\tools\crud\Action as BaseCrudAction;
use yii\tools\params\models\ActiveParam;
use admin\modules\setup\Finder;

/**
 * Class DefaultController
 * @package admin\modules\setup\controllers
 */
class DefaultController extends Controller
{
    /**
     * @var \admin\modules\setup\Finder
     */
    protected $finder;

    /**
     * @param string $id
     * @param \yii\base\Module $module
     * @param Finder $finder
     * @param array $config
     */
    public function __construct($id, $module, Finder $finder, $config = [])
    {
        $this->finder = $finder;
        parent::__construct($id, $module, $config);
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array_replace_recursive(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'update'  => ['post'],
                ],
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return array_merge(parent::actions(), [
            'index' => [
                'class' => 'yii\tools\crud\ReadAction',
                'modelPolicy' => BaseCrudAction::MODEL_POLICY_NONE,
                'afterCrudAction' => function ($event) {
                    $moduleId = Yii::$app->request->get('module');
                    if (!($event->action->params['models'] = $this->finder->findByModule($moduleId))) {
                        $event->result = $event->action->controller->redirect([RouteHelper::ADMIN_HOME]);
                        return;
                    }
                    $event->action->params['module'] = Yii::$app->getModule(ModuleHelper::ADMIN . '/' . $moduleId);
                }
            ],
            'update' => [
                'class' => 'yii\tools\crud\UpdateAction',
                'model' => ActiveParam::className(),
                'searchKey' => 'category',
                'requestKey' => 'module',
                'multiple' => true,
                'finder' => $this->finder,
                'afterCrudAction' => function ($event) {
                    $event->action->redirectSuccess = $event->action->redirectError =
                        ['index', 'module' => preg_replace('/.*\//', '', $event->action->model[0]->category)];
                },
            ]
        ]);
    }
}
