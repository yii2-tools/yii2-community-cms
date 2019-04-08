<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 28.03.2016 22:35
 * via Gii Model Generator
 */

namespace design\modules\packs\models\query;

/**
 * This is the ActiveQuery class for [[\design\modules\packs\models\DesignPack]].
 *
 * @see \design\modules\packs\models\DesignPack
 */
class DesignPackQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return \design\modules\packs\models\DesignPack[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \design\modules\packs\models\DesignPack|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
