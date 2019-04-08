<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 18.04.2016 18:25
 * via Gii Model Generator
 */

namespace site\modules\plugins\models\query;

/**
 * This is the ActiveQuery class for [[\site\modules\plugins\models\PluginData]].
 *
 * @see \site\modules\plugins\models\PluginData
 */
class PluginDataQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return \site\modules\plugins\models\PluginData[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \site\modules\plugins\models\PluginData|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
