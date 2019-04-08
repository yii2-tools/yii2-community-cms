<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 08.05.2016 00:25
 * via Gii Model Generator
 */

namespace site\modules\news\models\query;

/**
 * This is the ActiveQuery class for [[\site\modules\news\models\NewsRecord]].
 *
 * @see \site\modules\news\models\NewsRecord
 */
class NewsRecordQuery extends \yii\tools\secure\ActiveQuery
{
    public $secureEnabled = false;

    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return \site\modules\news\models\NewsRecord[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \site\modules\news\models\NewsRecord|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
