<?php

namespace app\modules\admin\controllers;

use app\modules\admin\components\Controller;

/**
 * Class DefaultController
 * @package app\modules\admin\controllers
 */
class DefaultController extends Controller
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'index' => [
                'class' => 'yii\tools\components\Action',
                'params' => ['module' => $this->module],
            ],
            'error' => [
                'class' => 'yii\web\ErrorAction',
                'view' => '@app/views/engine/error',
            ],
        ];
    }
}
