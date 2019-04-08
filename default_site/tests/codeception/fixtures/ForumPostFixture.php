<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 30.04.16 20:35
 */

namespace tests\codeception\fixtures;

use yii\test\ActiveFixture;

class ForumPostFixture extends ActiveFixture
{
    public $modelClass = 'site\modules\forum\models\Post';
}
