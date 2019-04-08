<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 16.04.16 15:18
 */

namespace app\modules\services;

use Yii;
use yii\base\Component;
use yii\caching\DbDependency;
use app\modules\services\models\Service;

class Finder extends Component
{
    /**
     * @param array|string|null $condition
     * @param bool $all
     * @return mixed
     */
    public function findService($condition = null, $all = false)
    {
//        $dependency = Yii::createObject([
//            'class' => DbDependency::className(),
//            'sql' => Service::CACHE_DEPENDENCY,
//            'reusable' => true,
//        ]);

        //return Service::getDb()->cache(function () use ($condition, $all) {
            $query = Service::find()->where($condition);
            return $all ? $query->all() : $query->one();
        //}, 0, $dependency);
    }
}
