<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 30.04.16 20:32
 */

namespace tests\codeception\fixtures;

use yii\test\DbFixture;

class ForumFixture extends DbFixture
{
    public $depends = [
        'tests\codeception\fixtures\ForumSectionFixture',
        'tests\codeception\fixtures\ForumSubforumFixture',
        'tests\codeception\fixtures\ForumTopicFixture',
        'tests\codeception\fixtures\ForumPostFixture',
    ];
}
