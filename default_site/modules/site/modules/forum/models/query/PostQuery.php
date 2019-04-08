<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 30.04.2016 10:43
 * via Gii Model Generator
 */

namespace site\modules\forum\models\query;

/**
 * This is the ActiveQuery class for [[\site\modules\forum\models\Post]].
 *
 * @see \site\modules\forum\models\Post
 */
class PostQuery extends \yii\tools\secure\ActiveQuery
{
    public $secureEnabled = false;

    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return \site\modules\forum\models\Post[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \site\modules\forum\models\Post|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
