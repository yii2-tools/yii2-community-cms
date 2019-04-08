<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 01.05.16 9:48
 */

namespace site\modules\forum\controllers;

use Yii;
use yii\tools\crud\Action as BaseCrudAction;

class SectionController extends EntityController
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return array_merge(parent::actions(), [
            'show' => [
                'class' => 'yii\tools\crud\ReadAction',
                'modelPolicy' => BaseCrudAction::MODEL_POLICY_NONE,
                'beforeCallback' => function ($action) {
                    $slug = Yii::$app->getRequest()->getQueryParam('section');
                    $action->params['section'] = $this->finder->findSection(['slug' => $slug]);
                    $action->controller->configureBreadcrumbs($action->params['section']);
                }
            ],
        ]);
    }
}
