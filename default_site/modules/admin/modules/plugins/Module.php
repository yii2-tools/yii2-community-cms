<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 22.02.2016 14:13
 * via Gii Module Generator
 */

namespace admin\modules\plugins;

use Yii;
use app\helpers\ModuleHelper;
use app\helpers\RouteHelper;
use app\modules\admin\components\Module as BaseModule;
use admin\modules\plugins\models\Search;

/**
 * Class Module
 * @package admin\modules\plugins
 */
class Module extends BaseModule
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        $activeCount = Yii::$container->get(Search::className())->activeCount();

        return [
            [
                'label' => Yii::t('app', 'Overview'),
                'description' => Yii::t(ModuleHelper::ADMIN_PLUGINS, 'Plugins management'),
                'url' => [RouteHelper::ADMIN_PLUGINS_MANAGEMENT],
                'right' => $activeCount ? ['label' => $activeCount, 'type' => 'success'] : null
            ],
        ];
    }
}
