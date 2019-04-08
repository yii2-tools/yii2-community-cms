<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 03.04.2016 19:12
 * via Gii Model Generator
 */

namespace design\modules\menu\models\query;

/**
 * This is the ActiveQuery class for [[\design\modules\menu\models\MenuItem]].
 *
 * @see \design\modules\menu\models\MenuItem
 */
class MenuItemQuery extends \yii\db\ActiveQuery
{
    public function position($position)
    {
        $this->andWhere(['=', 'position', $position]);

        return $this;
    }

    /**
     * @inheritdoc
     * @return \design\modules\menu\models\MenuItem[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \design\modules\menu\models\MenuItem|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
