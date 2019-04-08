<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 16.02.16 12:04
 */

namespace app\components\params;

use yii\helpers\ArrayHelper;
use yii\tools\helpers\FormatHelper;
use yii\tools\interfaces\ListValueHolder;
use yii\tools\params\models\ActiveParam;
use app\models\ListValue;

/**
 * Class ListParam
 * @package app\components\params
 */
class ListParam extends ActiveParam implements ListValueHolder
{
    /** @inheritdoc */
    public $type = FormatHelper::TYPE_LIST;

    /**
     * @inheritdoc
     */
    public function getListValues()
    {
        return $this->hasMany(ListValue::className(), ['list_id' => 'name']);
    }

    /**
     * @inheritdoc
     */
    public function getListValuesArray()
    {
        return ArrayHelper::map($this->listValues, 'id', 'value');
    }

    /**
     * @return mixed
     */
    public function getCurrentListValue()
    {
        return $this->{$this->listValueAttribute()};
    }

    /**
     * @param $value
     * @return mixed
     */
    public function setCurrentListValue($value)
    {
        parent::set($value);
    }

    /**
     * @param $value
     * @return mixed
     */
    public function listValueExists($value)
    {
        return ArrayHelper::keyExists($value, $this->getListValuesArray());
    }

    /**
     * Returns current value of ListValueHolder
     * @return mixed
     */
    public function listValueAttribute()
    {
        return 'value';
    }

    /**
     * @inheritdoc
     */
    public function getValue()
    {
        return $this->getCurrentListValue();
    }

    /**
     * @inheritdoc
     */
    public function setValue($value)
    {
        $this->setCurrentListValue($value);
    }
}
