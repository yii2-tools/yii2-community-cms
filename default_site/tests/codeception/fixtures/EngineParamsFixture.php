<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 18.03.16 17:27
 */

namespace tests\codeception\fixtures;

use app\helpers\ModuleHelper;
use tests\codeception\_support\fixtures\IndexedActiveFixture;
use Yii;
use yii\test\DbFixture;

class EngineParamsFixture extends IndexedActiveFixture
{
    public $modelClass = 'yii\tools\params\models\ActiveParam';
}