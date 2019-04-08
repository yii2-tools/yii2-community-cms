<?php

namespace admin\modules\users\controllers;

use Yii;
use yii\base\InvalidConfigException;
use yii\filters\VerbFilter;
use app\modules\admin\components\Controller;
use yii\tools\crud\Action as BaseCrudAction;
use admin\modules\users\models\Search;

/**
 * Class ItemsControllerAbstract
 * @package admin\modules\users\controllers
 */
abstract class ItemsControllerAbstract extends Controller
{
    /**
     * @param  string $name
     * @return \admin\modules\users\components\Item
     */
    abstract protected function getItem($name);

    /**
     * @var int
     */
    protected $type;
    
    /**
     * @var string
     */
    protected $modelClass;

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();

        if ($this->modelClass === null) {
            throw new InvalidConfigException('Model class should be set');
        }
        if ($this->type === null) {
            throw new InvalidConfigException('Auth item type should be set');
        }
    }

    /** @inheritdoc */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete'  => ['post'],
                ],
            ],
        ];
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
                'beforeCallback' => function ($action) {
                    $action->params['filterModel'] = new Search($action->controller->type);
                    $action->params['dataProvider'] = $action->params['filterModel']
                        ->search(Yii::$app->getRequest()->get());
                }
            ],
            'create' => [
                'class' => 'yii\tools\crud\CreateAction',
                'model' => $this->modelClass,
                'searchKey' => 'name',
                'redirectSuccess' => ['index'],
            ],
            'update' => [
                'class' => 'yii\tools\crud\UpdateAction',
                'model' => $this->modelClass,
                'searchKey' => 'name',
                'finder' => $this,
                'redirectSuccess' => ['index'],
            ],
            'delete' => [
                'class' => 'yii\tools\crud\DeleteAction',
                'model' => $this->modelClass,
                'searchKey' => 'name',
                'finder' => $this,
            ]
        ]);
    }

    public function getViewPath()
    {
        return $this->module->getViewPath() . DIRECTORY_SEPARATOR . 'auth_items' . DIRECTORY_SEPARATOR . $this->id;
    }

    public function findModel($name)
    {
        $model = Yii::createObject($this->modelClass);
        $model->setItem($this->getItem($name));
        return $model;
    }
}
