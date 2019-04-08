<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 27.02.16 15:59
 */

namespace app\helpers;

use yii\bootstrap\Html as BaseHtml;
use yii\helpers\ArrayHelper;

/**
 * Class Html
 * @package app\helpers
 */
class Html extends BaseHtml
{
    public static function faIcon($name, $options = [])
    {
        $tag = ArrayHelper::remove($options, 'tag', 'span');
        $spin = ArrayHelper::remove($options, 'spin') ? ' fa-spin' : '';
        $classPrefix = ArrayHelper::remove($options, 'prefix', "fa$spin fa-");
        static::addCssClass($options, $classPrefix . $name);

        return static::tag($tag, '', $options);
    }
}
