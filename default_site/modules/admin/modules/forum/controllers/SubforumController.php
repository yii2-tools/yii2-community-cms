<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 06.05.16 19:41
 */

namespace admin\modules\forum\controllers;

use Yii;
use app\helpers\RouteHelper;
use app\modules\admin\components\Controller;
use yii\tools\crud\Action as BaseCrudAction;
use admin\modules\forum\models\subforum\Search;

class SubforumController extends Controller
{
    /**
     * @var string
     */
    public $modelClass = 'site\modules\forum\models\Subforum';

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
            ],
            'create' => [
                'class' => 'yii\tools\crud\CreateAction',
                'model' => $this->modelClass,
                'redirectSuccess' => [RouteHelper::ADMIN_FORUM_SUBFORUMS],
            ],
            'update' => [
                'class' => 'yii\tools\crud\UpdateAction',
                'model' => $this->modelClass,
                'redirectSuccess' => [RouteHelper::ADMIN_FORUM_SUBFORUMS],
            ],
            'delete' => [
                'class' => 'yii\tools\crud\DeleteAction',
                'model' => $this->modelClass,
                'redirectSuccess' => [RouteHelper::ADMIN_FORUM_SUBFORUMS],
            ]
        ]);
    }
}
