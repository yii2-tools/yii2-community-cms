<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 30.04.2016 10:42
 * via Gii Model Generator
 */

namespace site\modules\forum\models\query;

/**
 * This is the ActiveQuery class for [[\site\modules\forum\models\Subforum]].
 *
 * @see \site\modules\forum\models\Subforum
 */
class SubforumQuery extends \yii\tools\secure\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return \site\modules\forum\models\Subforum[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \site\modules\forum\models\Subforum|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
