<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 18.03.16 16:02
 */

namespace tests\codeception\_pages\traits;

/**
 * Page which can contain any number of $_GET params
 * Example: /recovery/reset?key={key}
 * @package tests\codeception\_pages\traits
 */
trait ParameterizedPage
{
    /**
     * Example: ['key', 'id']
     * @var array
     */
    public static $params;

    protected static function ensureOpenedByCondition($I, $page, $options = [])
    {
        if (empty(static::$params)) {
            throw new \LogicException('Params of ParameterizedPage should be configured');
        }

        $queryRegex = '(' . static::buildQueryRegex() . ')?';
        $I->seeCurrentUrlMatches('|' . preg_quote($page->getUrl()) . $queryRegex . '|');
    }

    protected static function buildQueryRegex()
    {
        $params = static::$params;
        $first = array_shift($params);
        $other = '';
        foreach ($params as $param) {
            $other .= '&' . $param . '=.*';
        }

        return '\?' . $first . '=.*' . $other;
    }
}
