<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 30.04.16 20:34
 */

namespace tests\codeception\fixtures;

use yii\test\ActiveFixture;

class ForumSubforumFixture extends ActiveFixture
{
    public $modelClass = 'site\modules\forum\models\Subforum';
}
