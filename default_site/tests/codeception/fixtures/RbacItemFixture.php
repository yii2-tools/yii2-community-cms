<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 20.03.16 13:04
 */

namespace tests\codeception\fixtures;

use Yii;
use tests\codeception\_support\fixtures\IndexedActiveFixture;

class RbacItemFixture extends IndexedActiveFixture
{
    public $dataFile = '@migrations/source/db/yii2/rbac/data/items.php';
    public $dataColumnsFile = '@migrations/source/db/yii2/rbac/data/items_columns.php';

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->tableName = Yii::$app->getAuthManager()->itemTable;

        parent::init();
    }
}