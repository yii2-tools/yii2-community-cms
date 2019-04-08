<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 24.04.16 14:59
 */

namespace admin\modules\users\components;

use yii\rbac\Item as BaseItem;

/**
 * Class Item
 * @package admin\modules\users\components
 */
class Item extends BaseItem
{
    /**
     * @var boolean
     */
    public $active;
}
