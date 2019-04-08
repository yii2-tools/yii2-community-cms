<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 11.04.16 14:46
 */

namespace tests\codeception\fixtures;

use yii\test\ActiveFixture;

class MenuItemsFixture extends ActiveFixture
{
    public $modelClass = 'design\modules\menu\models\MenuItem';
}
