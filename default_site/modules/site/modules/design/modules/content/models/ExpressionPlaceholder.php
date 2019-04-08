<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 23.03.16 15:13
 */

namespace design\modules\content\models;

use Yii;

/**
 * Content placeholder (type 1) represents PHP statement
 * @package design\modules\content\models
 */
class ExpressionPlaceholder extends ActivePlaceholder
{
    /**
     * @inheritdoc
     */
    protected function evaluateInternal(array $params)
    {
        return eval($this->getExpression());
    }

    /**
     * @return string
     */
    protected function getExpression()
    {
        return Yii::$app->getFormatter()->asString($this->content);
    }
}
