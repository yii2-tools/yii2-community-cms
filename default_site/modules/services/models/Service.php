<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 16.04.2016 15:17
 * via Gii Model Generator
 */

namespace app\modules\services\models;

use Yii;
use yii\db\ActiveRecord;
use app\modules\services\models\query\ServiceQuery;

/**
 * This is the model class for table "{{%services}}".
 *
 * @property integer $service_id
 * @property string $service_key
 * @property string $service_name
 * @property string $service_params
 */
class Service extends ActiveRecord
{
//    const CACHE_DEPENDENCY = 'SELECT COUNT(*), MAX([[updated_at]]) FROM [[design_packs]] WHERE [[updated_at]] > 0';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%services}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['service_name'], 'required'],
            [['service_key'], 'string', 'max' => 32],
            [['service_name'], 'string', 'max' => 100],
            [['service_params'], 'string', 'max' => 200],
            [['service_key'], 'unique'],
            [['service_name'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'service_id' => 'Service ID',
            'service_key' => 'Service Key',
            'service_name' => 'Service Name',
            'service_params' => 'Service Params',
        ];
    }

    /**
     * @inheritdoc
     * @return ServiceQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ServiceQuery(get_called_class());
    }
}
