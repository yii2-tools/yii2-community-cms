<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 04.04.16 9:31
 */

namespace design\modules\packs;

use Yii;
use yii\base\Component;
use yii\caching\DbDependency;
use design\modules\packs\models\DesignPack;

class Finder extends Component
{
    /**
     * @param null $condition
     * @param bool $all
     * @return mixed
     */
    public function findDesignPack($condition = null, $all = false)
    {
        $dependency = Yii::createObject([
            'class' => DbDependency::className(),
            'sql' => DesignPack::CACHE_DEPENDENCY,
            'reusable' => true,
        ]);

        return DesignPack::getDb()->cache(function () use ($condition, $all) {
            $query = DesignPack::find()->where($condition);
            return $all ? $query->all() : $query->one();
        }, 0, $dependency);
    }
}
