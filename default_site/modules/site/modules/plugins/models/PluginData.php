<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 18.04.2016 18:25
 * via Gii Model Generator
 */

namespace site\modules\plugins\models;

use Yii;
use yii\db\ActiveRecord;
use site\modules\plugins\models\query\PluginDataQuery;

/**
 * This is the model class for table "{{%plugin_data}}".
 * Port from old engine 1.0
 *
 *
 * @property integer $plugin_data_id
 * @property string $plugin_key
 * @property string $pd_key1
 * @property string $pd_key2
 * @property string $pd_key3
 * @property string $pd_key4
 * @property string $pd_key5
 * @property string $pd_key6
 * @property string $pd_key7
 * @property string $pd_key8
 * @property string $pd_key9
 * @since 0.1
 */
class PluginData extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%plugin_data}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['plugin_key'], 'string', 'max' => 32],
            [
                ['pd_key1', 'pd_key2', 'pd_key3', 'pd_key4', 'pd_key5', 'pd_key6', 'pd_key7', 'pd_key8', 'pd_key9'],
                'string', 'max' => 400
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'plugin_data_id' => 'Plugin Data ID',
            'plugin_key' => 'Plugin Key',
            'pd_key1' => 'Pd Key1',
            'pd_key2' => 'Pd Key2',
            'pd_key3' => 'Pd Key3',
            'pd_key4' => 'Pd Key4',
            'pd_key5' => 'Pd Key5',
            'pd_key6' => 'Pd Key6',
            'pd_key7' => 'Pd Key7',
            'pd_key8' => 'Pd Key8',
            'pd_key9' => 'Pd Key9',
        ];
    }

    /**
     * @inheritdoc
     * @return PluginDataQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PluginDataQuery(get_called_class());
    }
}
