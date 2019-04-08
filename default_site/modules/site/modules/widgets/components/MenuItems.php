<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 03.04.16 3:42
 */

namespace site\modules\widgets\components;

use Yii;
use yii\base\Widget;
use yii\caching\DbDependency;
use app\helpers\ModuleHelper;
use app\helpers\RouteHelper;
use yii\tools\interfaces\UrlSourceInterface;
use design\modules\menu\models\MenuItem;

class MenuItems extends Widget
{
    public function run()
    {
        return $this->render('@site/modules/widgets/views/menu_items.php', [
            'items' => $this->getItems()
        ]);
    }

    /**
     * @return array
     */
    protected function getItems()
    {
        return array_merge($this->getPredefinedItems(), $this->getUserDefinedItems());
    }

    /**
     * @return array
     */
    protected function getPredefinedItems()
    {
        $items = [];
        $isAdmin = ($user = Yii::$app->getUser()->getIdentity()) ? $user->isAdmin : false;

        // @todo link to setup account page required (alternative for devices with extra small display)
        $items[] = [
            'options' => [
                'class' => 'hidden-xs hidden-sm hidden-md hidden-lg',
            ],
            'label' => Yii::t(ModuleHelper::USERS, 'Account setup'),
            'url' => '#',
            'isFallback' => true,
        ];

        if ($isAdmin) {
            $items[] = [
                'options' => [
                    'class' => 'hidden-sm hidden-md hidden-lg',
                ],
                'label' => Yii::t('admin', 'Admin Panel'),
                'url' => [RouteHelper::ADMIN_HOME],
                'isFallback' => true,
            ];
        }

        return $items;
    }

    /**
     * @return array
     */
    protected function getUserDefinedItems()
    {
        $dependency = Yii::createObject([
            'class' => DbDependency::className(),
            'sql' => MenuItem::CACHE_DEPENDENCY,
            'reusable' => true,
        ]);

        $items = MenuItem::getDb()->cache(function ($db) {
            return MenuItem::find()->orderBy(['position' => SORT_ASC])->all();
        }, 0, $dependency);

        $itemsArray = [];

        /** @var UrlSourceInterface $item */
        foreach ($items as $item) {
            $itemsArray[] = [
                'label' => $item->getLabel(),
                'url' => $item->getUrlSource(),
            ];
        }

        return $itemsArray;
    }
}
