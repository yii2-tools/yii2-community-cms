<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 21.03.2016 16:41
 * via Gii Module Generator
 */

namespace admin\modules\widgets;

use Yii;
use app\helpers\ModuleHelper;
use app\helpers\RouteHelper;
use app\modules\admin\components\Module as BaseModule;
use admin\modules\widgets\models\Search;

class Module extends BaseModule
{
    public function init()
    {
        // widgets require design module.
        Yii::$app->getModule(ModuleHelper::DESIGN);

        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        $activeCount = Yii::$container->get(Search::className())->activeCount();

        return [
            [
                'label' => Yii::t('app', 'Overview'),
                'description' => Yii::t(ModuleHelper::ADMIN_WIDGETS, 'Widgets management'),
                'url' => [RouteHelper::ADMIN_WIDGETS_MANAGEMENT],
                'right' => $activeCount ? ['label' => $activeCount, 'type' => 'success'] : null
            ],
        ];
    }
}
