<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 24.04.2016 13:48
 * via Gii Model Generator
 */

namespace site\modules\pages\models\query;

use yii\tools\secure\ActiveQuery as BaseActiveQuery;

/**
 * This is the ActiveQuery class for [[\site\modules\pages\models\Page]].
 *
 * @see \site\modules\pages\models\Page
 */
class PageQuery extends BaseActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return \site\modules\pages\models\Page[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \site\modules\pages\models\Page|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
