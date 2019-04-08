<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 29.04.16 15:35
 */

namespace tests\codeception\fixtures;

use tests\codeception\_support\fixtures\IndexedActiveFixture;

class EngineRoutingFixture extends IndexedActiveFixture
{
    public $modelClass = 'app\modules\routing\models\Route';
    public $dataColumnsFile = '@migrations/source/db/yii2-community-cms/engine/data/engine_routing_columns.php';
    public $dataFile = '@migrations/source/db/yii2-community-cms/engine/data/engine_routing.php';
}
