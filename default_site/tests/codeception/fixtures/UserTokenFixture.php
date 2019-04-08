<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 15.03.16 17:50
 */

namespace tests\codeception\fixtures;

use yii\test\ActiveFixture;

class UserTokenFixture extends ActiveFixture
{
    public $modelClass = 'site\modules\users\models\Token';
}