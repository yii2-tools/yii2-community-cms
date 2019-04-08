<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 22.02.2016 14:13
 * via Gii Module Generator
 */

namespace admin\modules\plugins\controllers;

use Yii;
use app\modules\admin\components\Controller;
use yii\tools\crud\Action as BaseCrudAction;
use integrations\modules\companyName\helpers\CompanyNameHelper as IntegrationHelper;
use admin\modules\plugins\models\Search;

/**
 * Class DefaultController
 * @package admin\modules\plugins\controllers
 */
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
                    'list'  => ['get'],
                    'get'  => ['post'],
                    'activate'  => ['post'],
                    'deactivate'  => ['post'],
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
                'operation' => IntegrationHelper::PLUGINS_GET,
                'redirect' => ['list']
            ],
            'activate' => [
                'class' => 'app\modules\integrations\components\actions\IntegrateAction',
                'vendor' => IntegrationHelper::COMPANY_NAME,
                'operation' => IntegrationHelper::PLUGINS_ACTIVATE,
                'redirect' => ['list']
            ],
            'update' => [
                'class' => 'app\modules\integrations\components\actions\IntegrateAction',
                'vendor' => IntegrationHelper::COMPANY_NAME,
                'operation' => IntegrationHelper::PLUGINS_UPDATE,
                'redirect' => ['list'],
            ],
            'deactivate' => [
                'class' => 'app\modules\integrations\components\actions\IntegrateAction',
                'vendor' => IntegrationHelper::COMPANY_NAME,
                'operation' => IntegrationHelper::PLUGINS_DEACTIVATE,
                'redirect' => ['list']
            ],
        ]);
    }
}
