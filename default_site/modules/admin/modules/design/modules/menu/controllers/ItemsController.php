<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 09.04.16 22:54
 */

namespace admin\modules\design\modules\menu\controllers;

use Yii;
use app\helpers\RouteHelper;
use app\modules\admin\components\Controller;
use admin\modules\design\modules\menu\assets\MenuAsset;
use admin\modules\design\modules\menu\Finder;

class ItemsController extends Controller
{
    public $modelClass = 'design\modules\menu\models\MenuItem';
    public $modelFormClass = 'admin\modules\design\modules\menu\models\ItemForm';

    /**
     * @var Finder
     */
    protected $finder;

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
    public function behaviors()
    {
        return array_merge_recursive(parent::behaviors(), [
            'verbs' => [
                'actions' => [
                    'position' => ['post'],
                ],
            ],
        ]);
    }

    public function actions()
    {
        return array_merge_recursive(parent::actions(), [
            'create' => [
                'class' => 'yii\tools\crud\CreateAction',
                'model' => $this->modelFormClass,
                'finder' => $this,
                'redirectSuccess' => [RouteHelper::ADMIN_DESIGN_MENU],
            ],
            'update' => [
                'class' => 'yii\tools\crud\UpdateAction',
                'model' => $this->modelFormClass,
                'finder' => $this,
                'redirectSuccess' => [RouteHelper::ADMIN_DESIGN_MENU],
            ],
            'position' => [
                'class' => 'yii\tools\components\PositionAction',
                'model' => $this->modelClass,
            ],
            'delete' => [
                'class' => 'yii\tools\crud\DeleteAction',
                'model' => $this->modelClass,
                'redirectSuccess' => [RouteHelper::ADMIN_DESIGN_MENU],
                'redirectError' => [RouteHelper::ADMIN_DESIGN_MENU],
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        MenuAsset::register($this->getView());
    }

    /**
     * @param $id
     * @return MenuItem
     */
    public function findModel($id)
    {
        if (!($item = $this->finder->findModel($id))) {
            return null;
        }
        $model = Yii::createObject($this->modelFormClass);
        $model->setItem($item);

        return $model;
    }
}
