<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 21.03.2016 16:39
 * via Gii Module Generator
 */

namespace admin\modules\design\controllers;

use Yii;
use app\modules\admin\components\Controller;
use yii\tools\crud\Action as BaseCrudAction;
use admin\modules\design\modules\packs\models\Search;

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
                    $action->params['uploadForm'] = Yii::createObject(
                        'admin\modules\design\modules\packs\models\UploadForm'
                    );
                    $action->params['filterModel'] = new Search();
                    $action->params['dataProvider'] = $action->params['filterModel']
                        ->search(Yii::$app->getRequest()->get());
                },
                'view' => '@admin/modules/design/modules/packs/views/default/index',
            ],
        ]);
    }
}
