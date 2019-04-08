<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 21.03.2016 16:40
 * via Gii Module Generator
 */

namespace admin\modules\forum\controllers;

use Yii;
use app\modules\admin\components\Controller;
use yii\tools\crud\Action as BaseCrudAction;
use admin\modules\forum\models\section\Search;

class DefaultController extends Controller
{
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
                    $action->params['searchModel'] = new Search();
                    $action->params['dataProvider'] = $action->params['searchModel']
                        ->search(Yii::$app->getRequest()->get());
                },
                'view' => '@admin/modules/forum/views/section/index',
            ],
        ]);
    }
}
