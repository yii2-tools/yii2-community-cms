<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 24.04.2016 13:48
 * via Gii Model Generator
 */

namespace site\modules\pages\models;

use Yii;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\tools\behaviors\CountableBehavior;
use yii\tools\secure\ActiveRecord;
use app\helpers\RouteHelper;
use app\helpers\ModuleHelper;
use app\modules\routing\behaviors\RoutableBehavior;
use site\modules\pages\Module;
use site\modules\pages\models\query\PageQuery;
use admin\modules\users\helpers\RbacHelper;

/**
 * This is the model class for table "{{%pages}}".
 *
 * @property integer $id
 * @property string $title
 * @property string $slug
 * @property string $content
 * @property integer $route_id
 * @property boolean $rbac_on
 * @property string $rbac_item
 * @property integer $created_at
 * @property integer $updated_at
 */
class Page extends ActiveRecord
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';
    const SCENARIO_DELETE = 'delete';

    const CACHE_DEPENDENCY = 'SELECT COUNT(*), MAX([[updated_at]]) FROM [[pages]] WHERE [[updated_at]] > 0';

    /**
     * @var Module
     */
    public $module;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->module = Yii::$app->getModule(ModuleHelper::PAGES);

        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array_merge(
            [
                'timestamp' => [
                    'class' => TimestampBehavior::className(),
                ],
                'sluggable' => [
                    'class' => SluggableBehavior::className(),
                    'attribute' => 'title',
                    'ensureUnique' => true,
                ],
                'countable' => [
                    'class' => CountableBehavior::className(),
                    'counterOwner' => $this->module,
                    'counterParam' => 'pages_count',
                ],
                'routable' => [
                    'class' => RoutableBehavior::className(),
                    'routeModule' => $this->module->getUniqueId(),
                    'routeAction' => RouteHelper::SITE_PAGES_SHOW,
                    'routeDescription' => Yii::t(ModuleHelper::ADMIN_PAGES, 'Page "{routeDescriptionParam}"'),
                    'routeDescriptionParam' => 'title',
                ]
            ],
            array_replace_recursive(parent::behaviors(), [
                'secure' => [
                    'secureRoles' => [RbacHelper::PAGES_ACCESS_CONTROL],
                    'secureItemDescription' => Yii::t(
                        ModuleHelper::ADMIN_PAGES,
                        'Access to page "{secureItemDescriptionParam}"'
                    ),
                    'secureItemDescriptionParam' => 'title',
                ]
            ])
        );
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%pages}}';
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return array_merge(parent::scenarios(), [
            static::SCENARIO_CREATE => ['title', 'content', 'route_id', 'rbac_on', 'rbac_item'],
            static::SCENARIO_UPDATE => ['title', 'content', 'route_id', 'rbac_on', 'rbac_item'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function transactions()
    {
        return [
            static::SCENARIO_DEFAULT => static::OP_ALL,
            static::SCENARIO_CREATE => static::OP_ALL,
            static::SCENARIO_UPDATE => static::OP_ALL,
            static::SCENARIO_DELETE => static::OP_ALL,
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['route_id'], 'default', 'value' => 0],
            [['title', 'slug', 'content', 'created_at', 'updated_at'], 'required'],
            [['content'], 'string'],
            [['created_at', 'updated_at'], 'integer'],
            [['rbac_on'], 'boolean'],
            [['title', 'slug', 'rbac_item'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'title' => Yii::t('app', 'Title'),
            'slug' => Yii::t('app', 'Slug'),
            'content' => Yii::t('app', 'Content'),
            'route_id' => Yii::t('app', 'Url'),
            'rbac_on' => Yii::t('app', 'Access control'),
            'rbac_item' => Yii::t('app', 'Access permission'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Edited At'),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function queryClassName()
    {
        return PageQuery::className();
    }
}
