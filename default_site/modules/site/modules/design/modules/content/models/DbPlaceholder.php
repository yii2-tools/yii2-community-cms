<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 23.03.16 15:09
 */

namespace design\modules\content\models;

/**
 * Wrapper skeleton for content placeholders
 * Represents data from database used as params for evaluate action of parent placeholder
 * @package design\modules\content\models
 */
abstract class DbPlaceholder extends StaticPlaceholder
{
    /**
     * @inheritdoc
     */
    public function isChildOnly()
    {
        return true;
    }
}
