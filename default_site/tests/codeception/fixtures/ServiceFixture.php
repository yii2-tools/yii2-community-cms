<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 16.04.16 21:21
 */

namespace tests\codeception\fixtures;

use yii\test\ActiveFixture;

class ServiceFixture extends ActiveFixture
{
    public $modelClass = 'app\modules\services\models\Service';
}
