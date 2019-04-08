<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 20.02.2016 15:39
 * via Gii Model Generator
 */

namespace app\modules\integrations\models\query;

/**
 * This is the ActiveQuery class for [[\app\modules\integrations\models\Integration]].
 *
 * @see \app\modules\integrations\models\Integration
 */
class IntegrationQuery extends \yii\db\ActiveQuery
{
    public function vendor($vendor)
    {
        $this->andWhere(['=', 'vendor', $vendor]);
        return $this;
    }

    public function category($category)
    {
        $this->andWhere(['=', 'category', $category]);
        return $this;
    }

    public function contextId($context_id)
    {
        $this->andWhere(['=', 'context_id', $context_id]);
        return $this;
    }

    /**
     * @inheritdoc
     * @return \app\modules\integrations\models\Integration[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\modules\integrations\models\Integration|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
