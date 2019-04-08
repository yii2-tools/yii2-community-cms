<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 21.03.2016 16:41
 * via Gii Module Generator
 */

namespace admin\modules\widgets\controllers;

use Yii;
use app\modules\admin\components\Controller;
use yii\tools\crud\Action as BaseCrudAction;
use integrations\modules\companyName\helpers\CompanyNameHelper as IntegrationHelper;
use admin\modules\widgets\models\Search;

class DefaultController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array_replace_recursive(parent::behaviors(), [
            'verbs' => [
                'actions' => [
                    'list'    => ['get'],
                    'get'     => ['post'],
                    'add'     => ['post'],
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
            'list' => [
                'class' => 'yii\tools\crud\ReadAction',
                'modelPolicy' => BaseCrudAction::MODEL_POLICY_NONE,
                'beforeCallback' => function ($action) {
                        $action->params['searchModel'] = Yii::createObject(Search::className());
                        $action->params['dataProvider'] = $action->params['searchModel']->search(Yii::$app->request->get());
                }
            ],
            'get' => [
                'class' => 'app\modules\integrations\components\actions\IntegrateAction',
                'vendor' => IntegrationHelper::COMPANY_NAME,
                'operation' => IntegrationHelper::WIDGETS_GET,
                'redirect' => ['list']
            ],
            'add' => [
                'class' => 'app\modules\integrations\components\actions\IntegrateAction',
                'vendor' => IntegrationHelper::COMPANY_NAME,
                'operation' => IntegrationHelper::WIDGETS_ADD,
                'redirect' => ['list']
            ],
            'update' => [
                'class' => 'app\modules\integrations\components\actions\IntegrateAction',
                'vendor' => IntegrationHelper::COMPANY_NAME,
                'operation' => IntegrationHelper::WIDGETS_UPDATE,
                'redirect' => ['list'],
            ],
            'delete' => [
                'class' => 'app\modules\integrations\components\actions\IntegrateAction',
                'vendor' => IntegrationHelper::COMPANY_NAME,
                'operation' => IntegrationHelper::WIDGETS_DELETE,
                'redirect' => ['list']
            ],
        ]);
    }
}
