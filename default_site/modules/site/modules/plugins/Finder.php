<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 18.04.16 18:37
 */

namespace site\modules\plugins;

use yii\base\Object;
use site\modules\plugins\models\PluginData;

class Finder extends Object
{
    /**
     * @param string|array $condition
     * @param bool $all
     * @return array|null|PluginData|PluginData[]
     */
    public function findPluginData($condition, $all = false)
    {
        $query = PluginData::find()->where($condition);

        return $all ? $query->all() : $query->one();
    }
}
