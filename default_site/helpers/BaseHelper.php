<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 31.03.16 6:29
 */

namespace app\helpers;

use ReflectionClass;

abstract class BaseHelper
{
    /**
     * This method designed to use in templates, for example:
     *
     * ```
     * {{ module('USERS') }}
     * ```
     *
     * This statement evaluates in appropriate constant value:
     * ModuleHelper::USERS => 'users'
     *
     * @param string $name
     * @return string
     * @throws \InvalidArgumentException
     */
    public static function constant($name)
    {
        if (($value = (new ReflectionClass(get_called_class()))->getConstant($name)) === false) {
            throw new \InvalidArgumentException("Constant '$name' doesn't exists");
        }

        return $value;
    }
}
