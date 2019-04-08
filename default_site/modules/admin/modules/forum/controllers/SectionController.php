<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 06.05.16 20:59
 */

namespace admin\modules\forum\controllers;

use Yii;
use app\helpers\RouteHelper;
use app\modules\admin\components\Controller;

class SectionController extends Controller
{
    /**
     * @var string
     */
    public $modelClass = 'site\modules\forum\models\Section';

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return array_merge(parent::actions(), [
            //'index' => ...        // in default controller
            'create' => [
                'class' => 'yii\tools\crud\CreateAction',
                'model' => $this->modelClass,
                'redirectSuccess' => [RouteHelper::ADMIN_FORUM],
            ],
            'update' => [
                'class' => 'yii\tools\crud\UpdateAction',
                'model' => $this->modelClass,
                'redirectSuccess' => [RouteHelper::ADMIN_FORUM],
            ],
            'delete' => [
                'class' => 'yii\tools\crud\DeleteAction',
                'model' => $this->modelClass,
                'redirectSuccess' => [RouteHelper::ADMIN_FORUM],
            ]
        ]);
    }
}
