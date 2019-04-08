<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 11.04.16 14:48
 */

namespace tests\codeception\fixtures;

use yii\test\DbFixture;

class MenuFixture extends DbFixture
{
    public $depends = [
        'tests\codeception\fixtures\MenuItemsFixture',
    ];
}
