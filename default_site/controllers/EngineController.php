<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;

class EngineController extends Controller
{
    public function actions()
    {
        return [
            'index' => [
                'class' => 'yii\tools\components\Action'
            ],
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }
}
