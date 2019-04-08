<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 20.02.2016 15:39
 * via Gii Model Generator
 */

namespace app\modules\integrations\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use app\modules\integrations\models\query\IntegrationQuery;

/**
 * This is the model class for table "{{%engine_integrations}}".
 *
 * @property integer $id
 * @property string $vendor
 * @property string $category
 * @property string $context_id
 * @property resource $data
 * @property string $serializer
 * @property integer $created_at
 * @property integer $updated_at
 */
class Integration extends ActiveRecord
{
    const CACHE_DEPENDENCY = 'SELECT MAX([[updated_at]]) FROM {{%engine_integrations}} WHERE [[updated_at]] > 0';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%engine_integrations}}';
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className()
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['vendor', 'created_at', 'updated_at'], 'required'],
            [['data'], 'string'],
            [['created_at', 'updated_at'], 'integer'],
            [['vendor', 'category', 'context_id', 'serializer'], 'string', 'max' => 255],
            [['vendor'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'vendor' => 'Vendor',
            'category' => 'Category',
            'context_id' => 'Context ID',
            'data' => 'Data',
            'serializer' => 'Serialize method',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @inheritdoc
     * @return IntegrationQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new IntegrationQuery(get_called_class());
    }
}
