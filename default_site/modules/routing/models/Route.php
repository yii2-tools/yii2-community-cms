<?php

namespace app\modules\routing\models;

use Yii;
use yii\helpers\Json;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use app\modules\routing\models\query\RouteQuery;

/**
 * This is the model class for table "engine_routing".
 *
 * @property integer $id
 * @property string $module
 * @property string $default_url_pattern
 * @property string $url_pattern
 * @property string $route_pattern
 * @property string $route
 * @property string $params (array of additional variables for controller)
 * @property string $description
 * @property integer $created_at
 * @property integer $updated_at
 */
class Route extends ActiveRecord
{
    const FIELD_ROUTE = 'route';                    // used by ActivePlaceholder
    const CACHE_KEY = 'engine_custom_url_rules';
    const CACHE_DEPENDENCY = 'SELECT COUNT(*), MAX([[updated_at]]) FROM [[engine_routing]] WHERE [[updated_at]] > 0';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%engine_routing}}';
    }

    /**
     * @inheritdoc
     */
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
            [['created_at', 'updated_at'], 'required'],
            [['created_at', 'updated_at'], 'integer'],
            [
                ['module', 'default_url_pattern', 'url_pattern', 'route_pattern', 'route', 'params', 'description'],
                'string', 'max' => 255
            ],
            [['url_pattern'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'module' => 'Module ID',
            'default_url_pattern' => 'Default Url Pattern',
            'url_pattern' => 'Url Pattern',
            'route_pattern' => 'Route Pattern',
            'route' => 'Route',
            'params' => 'Params',
            'description' => 'Description',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Returns actual description text.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return array
     */
    public function getParamsArray()
    {
        return Json::decode($this->params);
    }

    /**
     * @inheritdoc
     * @return RouteQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new RouteQuery(get_called_class());
    }
}
