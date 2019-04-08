<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 01.05.16 10:06
 */

namespace site\modules\forum\controllers;

use Yii;
use yii\data\Pagination;
use app\helpers\ModuleHelper;
use yii\tools\crud\Action as BaseCrudAction;

//
// @todo: refactor duplicate access checking code in actions.
//
class SubforumController extends EntityController
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
                    $sectionSlug = Yii::$app->getRequest()->getQueryParam('section');
                    if ($section = $this->finder->findSection(['slug' => $sectionSlug])) {
                        $slug = Yii::$app->getRequest()->getQueryParam('subforum');
                        if ($action->params['subforum'] = $this->finder->findSubforum(['slug' => $slug])) {
                            $action->controller->configureBreadcrumbs($action->params['subforum']);

                            $totalCount = $action->params['subforum']->getTopics()->count();
                            Yii::$container->set(Pagination::className(), [
                                'totalCount' => $totalCount,
                                'defaultPageSize' => 20,
                            ]);

                            return;
                        }
                    }

                    if (!empty($section)) {
                        $action->controller->configureSectionBreadcrumb($section);
                    }

                    $action->controller->registerBreadcrumb(
                        Yii::t(ModuleHelper::FORUM, "Subforum doesn't exists or access has been denied")
                    );
                }
            ],
        ]);
    }
}
