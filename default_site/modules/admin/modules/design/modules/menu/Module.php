<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 09.04.16 11:12
 */

namespace admin\modules\design\modules\menu;

use Yii;
use app\helpers\RouteHelper;
use app\modules\admin\components\Module as BaseModule;
use site\modules\design\helpers\ModuleHelper;
use admin\modules\design\modules\menu\Module as MenuModule;

class Module extends BaseModule
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return array_merge([
            [
                'label' => Yii::t(ModuleHelper::DESIGN, 'Menu'),
                'description' => Yii::t(ModuleHelper::ADMIN_DESIGN, 'Menu management'),
                'url' => [RouteHelper::ADMIN_DESIGN_MENU],
                'active' => Yii::$app->controller->module instanceof MenuModule
            ],
        ], parent::actions());
    }
}
