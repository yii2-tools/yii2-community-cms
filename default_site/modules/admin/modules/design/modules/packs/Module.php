<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 04.04.16 7:53
 */

namespace admin\modules\design\modules\packs;

use Yii;
use app\helpers\RouteHelper;
use app\modules\admin\components\Module as BaseModule;
use site\modules\design\helpers\ModuleHelper;

class Module extends BaseModule
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        // @todo uncomment after edit template implementation (2.0.x)

//        $designPacksModule = Yii::$app->getModule(ModuleHelper::DESIGN_PACKS);
//        $designPacks = Yii::createObject('design\modules\packs\Finder')->findDesignPack(null, true);
//        $designPacksItems = [];
//
//        foreach ($designPacks as $designPack) {
//            $item = [
//                'label' => $designPack->name,
//                'url' => [RouteHelper::ADMIN_DESIGN_PACKS_EDIT, 'name' => $designPack->name],
//                'icon' => 'file-archive-o',
//            ];
//
//            if ($designPacksModule->params['design_pack'] === $designPack->name) {
//                $item['linkOptions'] = ['class' => 'ttt'];
//                $item['icon'] = 'check';
//            }
//
//            $designPacksItems[] = $item;
//        }

        return array_merge([
            [
                'label' => Yii::t(ModuleHelper::DESIGN, 'Design packs'),
                'description' => Yii::t(ModuleHelper::ADMIN_DESIGN, 'Design packs management'),
                'url' => [RouteHelper::ADMIN_DESIGN],
                'right' => [
                    'label' => $this->module->siteModule->params['design_packs_count']
                ]
            ],
//            [
//                'label' => Yii::t('app', 'Edit'),
//                'url' => false,
//                'items' => $designPacksItems,
//                //'active' => Yii::$app->controller->action->id === 'edit',
//            ],
        ], parent::actions());
    }
}
