<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 21.03.2016 16:40
 * via Gii Module Generator
 */

namespace admin\modules\pages;

use Yii;
use yii\base\BootstrapInterface;
use app\helpers\ModuleHelper;
use app\helpers\RouteHelper;
use app\modules\admin\components\Module as BaseModule;

class Module extends BaseModule implements BootstrapInterface
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        $overview = [
            'label' => Yii::t('app', 'Overview'),
            'description' => Yii::t(ModuleHelper::ADMIN_PAGES, 'Pages management'),
            'url' => [RouteHelper::ADMIN_PAGES],
            'right' => [
                'label' => $this->siteModule->params['pages_count']
            ]
        ];

        if (in_array('/' . Yii::$app->requestedRoute, [RouteHelper::ADMIN_PAGES_UPDATE])) {
            $overview['active'] = true;
        }

        return [
            $overview,
            [
                'label' => Yii::t('app', 'Create'),
                'description' => Yii::t(ModuleHelper::ADMIN_PAGES, 'Create new page'),
                'url' => [RouteHelper::ADMIN_PAGES_CREATE],
            ]
        ];
    }
}
