<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 17.03.16 16:51
 */

namespace tests\codeception\fixtures;

use Yii;
use yii\test\ActiveFixture;

class RbacAssignmentFixture extends ActiveFixture
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->tableName = Yii::$app->getAuthManager()->assignmentTable;

        parent::init();
    }
}