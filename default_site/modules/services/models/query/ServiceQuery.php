<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 16.04.2016 15:17
 * via Gii Model Generator
 */

namespace app\modules\services\models\query;

/**
 * This is the ActiveQuery class for [[\app\modules\services\models\Service]].
 *
 * @see \app\modules\services\models\Service
 */
class ServiceQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return \app\modules\services\models\Service[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\modules\services\models\Service|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
