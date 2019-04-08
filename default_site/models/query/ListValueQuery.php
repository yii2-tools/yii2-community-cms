<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 09.02.2016 15:19
 * via Gii Model Generator
 */

namespace app\models\query;

/**
 * This is the ActiveQuery class for [[\app\models\ListValue]].
 *
 * @see \app\models\ListValue
 */
class ListValueQuery extends \yii\db\ActiveQuery
{
    public function byList($listId)
    {
        $this->andWhere(['list_id', $listId]);

        return $this;
    }

    /**
     * @inheritdoc
     * @return \app\models\ListValue[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\models\ListValue|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
