<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 15.03.16 16:50
 */

namespace tests\codeception\fixtures;

use yii\test\ActiveFixture;

class UserFixture extends ActiveFixture
{
    const PASSWORD = '123456';

    public $modelClass = 'site\modules\users\models\User';

    public $depends = [
        'tests\codeception\fixtures\UserProfileFixture',
        'tests\codeception\fixtures\UserTokenFixture',
        'tests\codeception\fixtures\SessionFixture',
        'tests\codeception\fixtures\RbacFixture',
        'tests\codeception\fixtures\EmailFixture',
    ];
}
