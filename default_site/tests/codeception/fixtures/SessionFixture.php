<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 17.03.16 17:01
 */

namespace tests\codeception\fixtures;

use Yii;
use yii\test\ActiveFixture;
use app\components\web\Session;

class SessionFixture extends ActiveFixture
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->tableName = (new Session)->sessionTable;

        parent::init();
    }
}