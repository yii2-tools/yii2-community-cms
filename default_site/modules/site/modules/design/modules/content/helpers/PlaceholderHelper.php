<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 23.03.16 14:38
 */

namespace design\modules\content\helpers;

use Yii;
use yii\caching\DbDependency;
use design\modules\content\interfaces\PlaceholderInterface;
use design\modules\content\models\ActivePlaceholder;
use design\modules\content\models\ExpressionPlaceholder;
use design\modules\content\models\ViewPlaceholder;
use design\modules\content\models\WidgetPlaceholder;

class PlaceholderHelper
{
    const STATUS_INACTIVE               = 0;
    const STATUS_ACTIVATION_REQUIRED    = 1;
    const STATUS_ACTIVE                 = 2;

    const PREFIX_WIDGET                 = 'WIDGET_';

    public static $types = ['expression', 'view', 'widget'];

    /**
     * Returns placeholder type number (1..~)
     * @param string $name
     * @return int
     * @throws \Exception
     */
    public static function getTypeNumber($name)
    {
        if (($index = array_search($name, static::$types)) === false) {
            throw new \InvalidArgumentException('Undefined type name: ' . $name);
        }

        return $index + 1;
    }

    /**
     * Returns placeholder type name by number (1..~)
     * @param int $type
     * @return string
     * @throws \Exception
     */
    public static function getTypeName($type)
    {
        $index = $type - 1;

        if (!isset(static::$types[$index])) {
            throw new \InvalidArgumentException('Undefined type number: ' . $type);
        }

        return static::$types[$index];
    }

    /**
     * Create new instance of ActivePlaceholder class (type based)
     * @param array $row
     * @return ActivePlaceholder
     */
    public static function create(array $row)
    {
        $placeholder = static::instantiateByArray($row);
        $placeholder->setAttributes($row);
        return $placeholder;
    }

    /**
     * Returns instantiated (by name) ActivePlaceholder object
     * @param string $name
     * @param bool $childs
     * @return ActivePlaceholder
     */
    public static function instantiateByName($name, $childs = false)
    {
        $dependency = Yii::$container->get(DbDependency::className(), [], [
            'sql' => ActivePlaceholder::CACHE_DEPENDENCY,
            'reusable' => true,
        ]);

        $placeholder = ActivePlaceholder::getDb()->cache(function ($db) use ($name) {
            return ActivePlaceholder::find()->name($name)->one();
        }, 0, $dependency);

        if ($childs) {
            static::instantiateChilds($placeholder);
        }

        return $placeholder;
    }

    /**
     * Note: only instantiate by type action WITHOUT ANY ATTRIBUTE
     *
     * @param array $row
     * @return ActivePlaceholder
     * @throws \LogicException
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public static function instantiateByArray(array $row)
    {
        $type = (int)$row['type'];

        if (ActivePlaceholder::TYPE_EXPRESSION === $type) {
            $placeholder = Yii::$container->get(ExpressionPlaceholder::className());
        } elseif (ActivePlaceholder::TYPE_VIEW === $type) {
            $placeholder = Yii::$container->get(ViewPlaceholder::className());
        } elseif (ActivePlaceholder::TYPE_WIDGET === $type) {
            $placeholder = Yii::$container->get(WidgetPlaceholder::className());
        } else {
            throw new \LogicException('Undefined type value of active placeholder record');
        }

        return $placeholder;
    }

    /**
     * @param PlaceholderInterface $placeholder
     * @param bool $recursive
     */
    public static function instantiateChilds(PlaceholderInterface $placeholder, $recursive = true)
    {
        if ($placeholder->isChildSupported() && $childs = $placeholder->getChilds()) {
            if ($recursive) {
                foreach ($childs as $child) {
                    static::instantiateChilds($child);
                }
            }
        }
    }
}
