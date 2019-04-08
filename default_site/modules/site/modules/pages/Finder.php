<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 26.04.16 11:17
 */

namespace site\modules\pages;

use Yii;
use yii\base\Object;
use yii\caching\DbDependency;
use site\modules\pages\models\Page;

class Finder extends Object
{
    /**
     * @param array|string $condition
     * @param bool $secure
     * @param bool $all
     * @return mixed
     */
    public function findPage($condition = null, $secure = true, $all = false)
    {
        // WARNING: Secure behavior requires monitoring of rbac assigments COUNT(*) and 'updated_at' field.

//        $dependency = Yii::createObject([
//            'class' => DbDependency::className(),
//            'sql' => Page::CACHE_DEPENDENCY,
//            'reusable' => true,
//        ]);

        //return Page::getDb()->cache(function () use ($condition, $secure, $all) {
            $query = Page::find()->where($condition)->secure($secure);
            return $all ? $query->all() : $query->one();
        //}, 0, $dependency);
    }
}
