<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 13.03.16 8:50
 */

namespace app\helpers;

use yii\helpers\ArrayHelper as BaseArrayHelper;

/**
 * @package app\helpers
 * @since 2.0.0
 */
class ArrayHelper extends BaseArrayHelper
{
    /**
     * @param $array
     * @param array $names Indexed array!
     * @param null $default
     * @return array
     * @throws \LogicException
     */
    public static function getValues($array, array $names, $default = null)
    {
        if (!static::isIndexed($names)) {
            throw new \LogicException('$names must be indexed array');
        }
        $values = [];
        foreach ($names as $name) {
            $values[] = static::getValue($array, $name, $default);
        }
        return $values;
    }
}
