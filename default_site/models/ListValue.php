<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 09.02.2016 15:19
 * via Gii Model Generator
 */

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%engine_list_values}}".
 *
 * @property integer $id
 * @property integer $list_id
 * @property string $value
 */
class ListValue extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%engine_list_values}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'class' => TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['list_id', 'created_at', 'updated_at'], 'required'],
            [['list_id', 'created_at', 'updated_at'], 'integer'],
            [['value'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        // @todo Yii::t() yii2-active-params
        return [
            'id' => 'ID',
            'list_id' => 'List ID',
            'value' => 'Value',
        ];
    }

    /**
     * @inheritdoc
     * @return \app\models\query\ListValueQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\query\ListValueQuery(get_called_class());
    }
}
