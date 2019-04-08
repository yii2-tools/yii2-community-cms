<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 03.04.2016 19:12
 * via Gii Model Generator
 */

namespace design\modules\menu\models;

use Yii;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\tools\interfaces\UrlSourceInterface;
use yii\tools\behaviors\PositionBehavior;
use app\modules\routing\models\Route;
use design\modules\menu\models\query\MenuItemQuery;

/**
 * This is the model class for table "{{%design_menu_items}}".
 *
 * @property integer $id
 * @property string $label
 * @property string $url_to
 * @property boolean $is_route
 * @property integer $position
 * @property integer $created_at
 * @property integer $updated_at
 */
class MenuItem extends ActiveRecord implements UrlSourceInterface
{
    const CACHE_DEPENDENCY = 'SELECT COUNT(*), MAX([[updated_at]]) FROM [[design_menu_items]] WHERE [[updated_at]] > 0';

    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%design_menu_items}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
            ],
            'position' => [
                'class' => PositionBehavior::className(),
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return array_merge(parent::scenarios(), [
            static::SCENARIO_CREATE => ['label', 'url_to', 'is_route'],
            static::SCENARIO_UPDATE => ['label', 'url_to', 'is_route'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['position', 'created_at', 'updated_at'], 'integer'],
            [['is_route'], 'boolean'],
            [['label', 'url_to', 'created_at', 'updated_at'], 'required'],
            [['label', 'url_to'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'label' => Yii::t('app', 'Title'),
            'url_to' => Yii::t('app', 'Url'),
            'is_route' => Yii::t('app', 'Route'),
            'position' => Yii::t('app', 'Position'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * Returns label suited for link to this concrete entity instance
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Returns Url::to result for property $url_to
     * @return string
     */
    public function getUrl()
    {
        return Url::to($this->getUrlSource(), true);
    }

    /**
     * Returns [route] or 'url' based on property $is_route
     * @return array|string
     */
    public function getUrlSource()
    {
        return $this->is_route
            ? Yii::$app->getUrlManager()->createUrl($this->route->url_pattern)
            : $this->url_to;
    }

    /**
     * Returns GET params for $url_to property (in context of local route)
     * @return array
     */
    public function getUrlParams()
    {
        return Json::decode($this->route->params);
    }

    /**
     * Route exists only if is_route is true.
     *
     * @return ActiveQuery
     */
    public function getRoute()
    {
        return $this->hasOne(Route::className(), ['id' => 'url_to']);
    }

    /**
     * @inheritdoc
     * @return MenuItemQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new MenuItemQuery(get_called_class());
    }
}
