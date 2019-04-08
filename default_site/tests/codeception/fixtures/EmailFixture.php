<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 16.03.16 17:37
 */

namespace tests\codeception\fixtures;

use Yii;
use yii\helpers\FileHelper;
use yii\test\Fixture;

class EmailFixture extends Fixture
{
    public function load()
    {
        parent::load();

        FileHelper::removeDirectory(Yii::getAlias('@runtime/mail'));
    }
}