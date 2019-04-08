<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 30.04.2016 10:42
 * via Gii Model Generator
 */

namespace site\modules\forum\models\query;

/**
 * This is the ActiveQuery class for [[\site\modules\forum\models\Section]].
 *
 * @see \site\modules\forum\models\Section
 */
class SectionQuery extends \yii\tools\secure\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return \site\modules\forum\models\Section[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \site\modules\forum\models\Section|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
