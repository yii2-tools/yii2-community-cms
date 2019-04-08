<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 09.03.2016 06:23
 * via Gii Model Generator
 */

namespace app\modules\routing\models\query;

/**
 * This is the ActiveQuery class for [[\app\modules\routing\models\Route]].
 *
 * @see \app\modules\routing\models\Route
 */
class RouteQuery extends \yii\db\ActiveQuery
{
    public function route($route)
    {
        $this->andWhere(['=', 'route', $route]);

        return $this;
    }

    public function urlPattern($urlPattern)
    {
        $this->andWhere(['=', 'url_pattern', $urlPattern]);

        return $this;
    }

    /**
     * @inheritdoc
     * @return \app\modules\routing\models\Route[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\modules\routing\models\Route|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
