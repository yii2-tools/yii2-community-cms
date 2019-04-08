<?php

namespace app\modules\migrations\models;

use Yii;

/**
 * This is the model class for table "engine_migrations".
 *
 * @property string $version
 * @property string $alias
 * @property integer $apply_time
 */
class EngineDbMigration extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%engine_migrations}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['version', 'alias', 'apply_time'], 'required'],
            [['apply_time'], 'integer'],
            [['version', 'alias'], 'string', 'max' => 180]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'version' => 'Version',
            'alias' => 'Alias',
            'apply_time' => 'Apply Time',
        ];
    }
}
