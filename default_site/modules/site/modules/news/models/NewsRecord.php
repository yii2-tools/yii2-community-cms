<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 08.05.2016 00:25
 * via Gii Model Generator
 */

namespace site\modules\news\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\SluggableBehavior;
use yii\db\BaseActiveRecord;
use yii\tools\behaviors\CountableBehavior;
use yii\tools\behaviors\HtmlPurifierBehavior;
use app\helpers\ModuleHelper;
use app\traits\DateFieldTrait;
use yii\tools\secure\ActiveRecord;
use site\modules\news\Module;
use site\modules\news\models\query\NewsRecordQuery;
use site\modules\users\models\User;
use admin\modules\users\helpers\RbacHelper;

/**
 * This is the model class for table "{{%news}}".
 *
 * @property integer $id
 * @property string $title
 * @property string $slug
 * @property string $content
 * @property bool $rbac_on
 * @property string $rbac_item
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $created_at
 * @property integer $updated_at
 */
class NewsRecord extends ActiveRecord
{
    use DateFieldTrait;

    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';
    const SCENARIO_DELETE = 'delete';

    /**
     * @var Module
     */
    public $module;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->module = Yii::$app->getModule(ModuleHelper::NEWS);

        parent::init();
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%news}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array_merge(
            [
                'htmlpurifier' => [
                    'class' => HtmlPurifierBehavior::className(),
                ],
                'timestamp' => [
                    'class' => TimestampBehavior::className(),
                ],
                'blameable' => [
                    'class' => BlameableBehavior::className(),
                    'attributes' => [
                        BaseActiveRecord::EVENT_BEFORE_INSERT => 'created_by',
                        BaseActiveRecord::EVENT_BEFORE_UPDATE => 'updated_by',
                    ],
                ],
                'sluggable' => [
                    'class' => SluggableBehavior::className(),
                    'attribute' => 'title',
                    'ensureUnique' => true,
                ],
                'countable' => [
                    'class' => CountableBehavior::className(),
                    'counterOwner' => $this->module,
                    'counterParam' => 'news_count',
                ],
            ],
            array_replace_recursive(parent::behaviors(), [
                'secure' => [
                    'secureRoles' => [RbacHelper::NEWS_ACCESS_CONTROL],
                    'secureItemTemplate' => 'ACCESS_NEWS',
                    'secureItemDescription' => Yii::t(
                        ModuleHelper::ADMIN_NEWS,
                        'Access to news record "{secureItemDescriptionParam}"'
                    ),
                    'secureItemDescriptionParam' => 'title',
                ]
            ])
        );
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return array_merge(parent::scenarios(), [
            static::SCENARIO_CREATE => ['title', 'content', 'rbac_on', 'rbac_item'],
            static::SCENARIO_UPDATE => ['title', 'content', 'rbac_on', 'rbac_item'],
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
            [['title', 'slug', 'content', 'created_by', 'created_at', 'updated_at'], 'required'],
            [['content'], 'string'],
            [['created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['rbac_on'], 'boolean'],
            [['title', 'slug', 'rbac_item'], 'string', 'max' => 255],
            [['slug'], 'unique']
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
            'rbac_on' => Yii::t('app', 'Rbac On'),
            'rbac_item' => Yii::t('app', 'Rbac Item'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery|null
     */
    public function getAuthor()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery|null
     */
    public function getEditor()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }

    /**
     * @inheritdoc
     * @return NewsRecordQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new NewsRecordQuery(get_called_class());
    }
}
