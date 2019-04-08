<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 20.03.16 13:04
 */

namespace tests\codeception\fixtures;

use Yii;
use yii\test\DbFixture;

class RbacFixture extends DbFixture
{
    public $depends = [
        'tests\codeception\fixtures\RbacRuleFixture',
        'tests\codeception\fixtures\RbacItemFixture',
        'tests\codeception\fixtures\RbacItemChildFixture',
        'tests\codeception\fixtures\RbacAssignmentFixture',
    ];
}
