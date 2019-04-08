<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 23.03.2016 14:20
 * via Gii Model Generator
 */

namespace design\modules\content\models\query;

use yii\db\Expression;
use design\modules\content\models\ActivePlaceholder;

/**
 * This is the ActiveQuery class for [[\design\modules\content\models\ActivePlaceholder]].
 *
 * @see \design\modules\content\models\ActivePlaceholder
 */
class ActivePlaceholderQuery extends \yii\db\ActiveQuery
{
    public function name($name)
    {
        $this->andWhere(['=', 'name', $name]);

        return $this;
    }

    public function type($type)
    {
        $this->andWhere(['=', 'type', $type]);

        return $this;
    }

    /**
     * @param string $route
     * @param string $sourceAlias   Alias will be used for source (main) table, not for junction
     * @return $this
     */
    public function route($route, $sourceAlias = 't')
    {
        $this->alias($sourceAlias);
        $relationTable = ActivePlaceholder::tableNameRelationRoutes();
        $on = ActivePlaceholder::relationTableRoutesOn();
        $this->innerJoin(
            $relationTable . ' j',
            ['=', $sourceAlias . '.[[' . ActivePlaceholder::primaryKey()[0] . ']]', new Expression("j.[[{$on[0]}]]")]
        );
        $this->andWhere(new Expression(":route LIKE j.[[{$on[1]}]]", [':route' => $route]));

        return $this;
    }

    public function status($status)
    {
        $this->andWhere(['=', 'status', $status]);

        return $this;
    }

    /**
     * @inheritdoc
     * @return \design\modules\content\models\ActivePlaceholder[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \design\modules\content\models\ActivePlaceholder|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
