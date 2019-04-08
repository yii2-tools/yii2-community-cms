<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 09.02.16 14:49
 */

namespace app\components;

use Html2Text\Html2Text;
use yii\base\InvalidParamException;
use yii\i18n\Formatter as BaseFormatter;

/**
 * Class Formatter
 * @package app\components
 */
class Formatter extends BaseFormatter
{
    /**
     * Parses route string and return all parts after $offset number of slashes ('/')
     *
     * Example:
     *      input: /site/users/security/login, offset = 2
     *      output: users/security/login
     *
     * @param string $value route
     * @param int $offset number of slashes ('/') from what result route string will be returned
     * @return string
     */
    public function asRoute($value, $offset = -1)
    {
        $value = $this->normalizeRoute($value);
        return preg_replace('/[^\/]*\//', '', $value, $offset);
    }

    /**
     * Retrieves body from route string
     *
     * Example:
     *     input: /site/users/security/login, tailOffset = 2, headOffset = 1 (default behavior)
     *     output: users/security
     *         (removed tail parts: /, site/
     *          removed head parts: /login)
     *
     * @param string $value route
     * @param int $tailOffset number of slashes ('/') in tail part of route string
     * @param int $headOffset number of slashes ('/') in head part of route string
     * @return string
     */
    public function asRouteBody($value, $tailOffset = 2, $headOffset = 1)
    {
        $value = $this->normalizeRoute($value);
        $routeParts = explode('/', $value);
        return implode('/', array_slice($routeParts, $tailOffset, count($routeParts) - $headOffset));
    }

    /**
     * @param $value
     * @return int
     */
    public function asIntVal($value)
    {
        return intval($value);
    }

    /**
     * @param $value
     * @return string
     */
    public function asString($value)
    {
        return strval($value);
    }

    /**
     * @param $value
     * @return string
     */
    protected function normalizeRoute($value)
    {
        if ($value[0] !== '/') {
            $value = '/' . $value;
        }

        return $value;
    }

    /**
     * @param mixed $value
     * @return string
     * @throws \yii\base\InvalidParamException
     */
    protected function normalizeVersionString($value)
    {
        if (is_string($value)) {
            return $value;
        } elseif (is_object($value) && method_exists($value, '__toString')) {
            return call_user_func([$value, '__toString']);
        } elseif (is_int($value)) {
            return strval($value);
        }

        throw new InvalidParamException("'$value' is not a string value.");
    }

    /**
     * @param $value
     * @return string
     */
    public function asRawText($value)
    {
        return Html2Text::convert($value);
    }

    /**
     * @param $value
     * @return string
     */
    public function asNumericBoolean($value)
    {
        return $value ? '1' : '0';
    }

    /**
     * Returns string value what represents pretty-readable version, i.e. 200 => 2.0.0
     *
     * @param int $value version number, i.e. 200
     * @param int $sequence number of positions in version (.0)
     * @return string
     */
    public function asVersionString($value, $sequence = 3)
    {
        $this->normalizeNumericValue($value);
        return str_pad(implode(".", str_split($value)), $sequence * 2 - 1, '.0');
    }

    /**
     * Returns integer value what represents version, i.e. 2.0.0 => 200
     *
     * WARNING: do not use this method for versions with position values > 9,
     * i.e. 2.0.11 become 2011 and 2.1.0 (210) will not be greater than 2.0.11 (2011)
     *
     * @param string $value version string, i.e. 2.0.0
     * @return int
     */
    public function asVersionInteger($value)
    {
        $this->normalizeVersionString($value);
        return $this->asIntVal(preg_replace('/[^\d]/', '', $value));
    }
}
