<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 30.04.2016 10:43
 * via Gii Model Generator
 */

namespace site\modules\forum\models;

use Yii;
use yii\helpers\HtmlPurifier;
use yii\helpers\StringHelper;
use yii\behaviors\BlameableBehavior;
use yii\db\BaseActiveRecord;
use yii\tools\behaviors\CountableBehavior;
use yii\tools\behaviors\HtmlPurifierBehavior;
use app\helpers\ModuleHelper;
use app\traits\DateFieldTrait;
use site\modules\users\models\User;
use site\modules\forum\models\query\PostQuery;

/**
 * This is the model class for table "{{%forum_posts}}".
 *
 * @property integer $id
 * @property integer $topic_id
 * @property string $content
 * @property integer $is_first
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $created_at
 * @property integer $updated_at
 */
class Post extends Entity
{
    use DateFieldTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%forum_posts}}';
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
                'blameable' => [
                    'class' => BlameableBehavior::className(),
                    'attributes' => [
                        BaseActiveRecord::EVENT_BEFORE_INSERT => 'created_by',
                        BaseActiveRecord::EVENT_BEFORE_UPDATE => 'updated_by',
                    ],
                ],
                'countable' => [
                    'class' => CountableBehavior::className(),
                    'counterOwner' => $this->module,
                    'counterParam' => 'forum_posts_count',
                ],
            ],
            array_replace_recursive(parent::behaviors(), [
                'secure' => [
                    'secureRoles' => [],//[RbacHelper::FORUM_POSTS_ACCESS_CONTROL],
                    'secureItemTemplate' => 'ACCESS_FORUM_POSTS',
                    'secureItemDescription' => Yii::t(
                        ModuleHelper::ADMIN_FORUM,
                        'Access to forum post "{secureItemDescriptionParam}"'
                    ),
                    'secureItemDescriptionParam' => 'id',
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
            static::SCENARIO_CREATE => ['topic_id', 'content'],
            static::SCENARIO_UPDATE => ['content'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['topic_id', 'content', 'created_by', 'created_at', 'updated_at'], 'required'],
            [['topic_id', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['is_first'], 'boolean'],
            [['content'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'topic_id' => Yii::t(ModuleHelper::FORUM, 'Topic ID'),
            'content' => Yii::t('app', 'Content'),
            'is_first' => Yii::t(ModuleHelper::FORUM, 'Is First'),
            'rbac_on' => Yii::t('app', 'Access control'),
            'rbac_item' => Yii::t('app', 'Access permission'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @return string
     */
    public function getShortContent()
    {
        return StringHelper::truncate(HtmlPurifier::process(strip_tags($this->content)), 18);
    }

    /**
     * Returns topic that contains this post.
     * @return \yii\db\ActiveQuery|null
     */
    public function getTopic()
    {
        return $this->hasOne(Topic::className(), ['id' => 'topic_id'])->secure(true);
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
     */
    public static function queryClassName()
    {
        return PostQuery::className();
    }
}
