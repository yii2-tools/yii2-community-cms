<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 20.03.16 13:09
 */

namespace tests\codeception\fixtures;

use Yii;
use tests\codeception\_support\fixtures\IndexedActiveFixture;

class RbacRuleFixture extends IndexedActiveFixture
{
    public $dataFile = '@migrations/source/db/yii2/rbac/data/rules.php';
    public $dataColumnsFile = '@migrations/source/db/yii2/rbac/data/rules_columns.php';

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->tableName = Yii::$app->getAuthManager()->ruleTable;

        parent::init();
    }
}