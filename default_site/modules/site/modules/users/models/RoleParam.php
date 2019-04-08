<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 17.02.16 8:51
 */

namespace site\modules\users\models;

use Yii;
use yii\base\NotSupportedException;
use yii\helpers\ArrayHelper;
use app\components\params\ListParam;

/**
 * Class RoleParam
 * @package site\modules\users\models\RoleParam
 */
class RoleParam extends ListParam
{
    /**
     * RoleParam don't have ListValue relations
     * @throws \yii\base\NotSupportedException
     * @return void
     */
    public function getListValues()
    {
        throw new NotSupportedException(__CLASS__ . " don't have actual relation with ListValue records");
    }

    public function getListValuesArray()
    {
        return ArrayHelper::map(Yii::$app->getAuthManager()->getRoles(), 'name', 'name');
    }
}
