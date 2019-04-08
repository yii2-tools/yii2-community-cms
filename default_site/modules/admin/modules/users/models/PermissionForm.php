<?php

namespace admin\modules\users\models;

use Yii;
use yii\helpers\ArrayHelper;
use admin\modules\users\components\Item;

/**
 * Class Permission
 * @package admin\modules\users\models
 */
class PermissionForm extends AuthItemForm
{
    /** @inheritdoc */
    public function getUnassignedItems()
    {
        return ArrayHelper::map(
            $this->manager->getItems(Item::TYPE_PERMISSION, $this->item !== null ? [$this->item->name] : []),
            'name',
            function ($item) {
                return empty($item->description) ? $item->name : $item->name . ' (' . $item->description . ')';
            }
        );
    }

    /** @inheritdoc */
    protected function createItem($name)
    {
        return $this->manager->createPermission($name);
    }
}
