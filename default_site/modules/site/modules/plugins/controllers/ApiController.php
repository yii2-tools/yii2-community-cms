<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 17.04.16 19:37
 */

namespace site\modules\plugins\controllers;

use Yii;
use yii\web\Response;
use app\modules\site\components\Controller;

class ApiController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array_replace_recursive(parent::behaviors(), [
            'verbs' => [
                'actions' => [
                    'request' => ['post'],
                ],
            ],
        ]);
    }

    public function actionRequest()
    {
        $data = Yii::$app->getRequest()->getBodyParams();
        $responseData = $this->module->getClient()->call($data);

        $response = Yii::$app->getResponse();
        $response->format = Response::FORMAT_JSON;
        $response->data = $responseData;

        return $response;
    }
}
