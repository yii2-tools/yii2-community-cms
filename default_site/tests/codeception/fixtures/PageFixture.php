<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 29.04.16 14:00
 */

namespace tests\codeception\fixtures;

use tests\codeception\_support\fixtures\IndexedActiveFixture;

class PageFixture extends IndexedActiveFixture
{
    public $modelClass = 'site\modules\pages\models\Page';
    public $dataColumnsFile = '@migrations/source/db/yii2-community-cms/pages/data/pages_columns.php';
    public $dataFile = '@migrations/source/db/yii2-community-cms/pages/data/pages.php';

    public $depends = [
        'tests\codeception\fixtures\RbacFixture',
        'tests\codeception\fixtures\EngineRoutingFixture',
    ];
}
