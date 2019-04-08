<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 30.04.2016 10:42
 * via Gii Model Generator
 */

namespace site\modules\forum\models;

use Yii;
use yii\behaviors\SluggableBehavior;
use yii\data\Pagination;
use app\helpers\ModuleHelper;
use yii\tools\behaviors\CountableBehavior;
use yii\tools\behaviors\PositionBehavior;
use site\modules\forum\models\query\SubforumQuery;

/**
 * This is the model class for table "{{%forum_subforums}}".
 *
 * @property integer $id
 * @property integer $section_id
 * @property string $title
 * @property string $slug
 * @property string $description
 * @property integer $topics_num
 * @property integer $posts_num
 * @property integer $last_post_id
 * @property integer $position
 * @property integer $created_at
 * @property integer $updated_at
 */
class Subforum extends Entity
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%forum_subforums}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array_merge(
            [
                'position' => [
                    'class' => PositionBehavior::className(),
                ],
                'sluggable' => [
                    'class' => SluggableBehavior::className(),
                    'attribute' => 'title',
                    'ensureUnique' => true,
                ],
                'countable' => [
                    'class' => CountableBehavior::className(),
                    'counterOwner' => $this->module,
                    'counterParam' => 'forum_subforums_count',
                ],
            ],
            array_replace_recursive(parent::behaviors(), [
                'secure' => [
                    'secureRoles' => [],//[RbacHelper::FORUM_SUBFORUMS_ACCESS_CONTROL],
                    'secureItemTemplate' => 'ACCESS_FORUM_SUBFORUMS',
                    'secureItemDescription' => Yii::t(
                        ModuleHelper::ADMIN_FORUM,
                        'Access to subforum "{secureItemDescriptionParam}"'
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
            static::SCENARIO_CREATE => ['title', 'description', 'section_id', 'rbac_on', 'rbac_item'],
            static::SCENARIO_UPDATE => ['title', 'description', 'section_id', 'rbac_on', 'rbac_item'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['section_id', 'created_at', 'updated_at'], 'required'],
            [['section_id', 'topics_num', 'posts_num', 'last_post_id',
                'position', 'created_at', 'updated_at'], 'integer'],
            [['title', 'slug'], 'string', 'max' => 255],
            [['description'], 'string', 'max' => 400],
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
            'section_id' => Yii::t(ModuleHelper::FORUM, 'Section'),
            'title' => Yii::t('app', 'Title'),
            'slug' => Yii::t('app', 'Slug'),
            'description' => Yii::t('app', 'Description'),
            'topics_num' => Yii::t(ModuleHelper::FORUM, 'Topics'),
            'posts_num' => Yii::t(ModuleHelper::FORUM, 'Posts'),
            'last_post_id' => Yii::t(ModuleHelper::FORUM, 'Last Post ID'),
            'position' => Yii::t('app', 'Position'),
            'rbac_on' => Yii::t('app', 'Access control'),
            'rbac_item' => Yii::t('app', 'Access permission'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * Returns section that contains this subforum.
     * @return Section|null
     */
    public function getSection()
    {
        return $this->hasOne(Section::className(), ['id' => 'section_id'])->secure(true);
    }

    /**
     * Returns all topics that belongs to this subforum.
     * @return \yii\db\ActiveQuery|null
     */
    public function getTopics()
    {
        return $this->hasMany(Topic::className(), ['subforum_id' => 'id'])
            ->secure(true)
            ->orderBy(['updated_at' => SORT_DESC]);
    }

    /**
     * @return \yii\db\ActiveQuery|null
     */
    public function getFixedTopics()
    {
        return $this->getTopics()->andWhere(['=', 'is_fixed', 1]);
    }

    /**
     * @return \yii\db\ActiveQuery|null
     */
    public function getNonFixedTopics()
    {
        return $this->getTopics()->andWhere(['=', 'is_fixed', 0]);
    }

    /**
     * Returns NOT FIXED topics on current page (depends on Pagination state).
     * @return \yii\db\ActiveQuery|null
     */
    public function getTopicsOnPage()
    {
        /** @var Pagination $pagination */
        $pagination = Yii::$container->get(Pagination::className());

        $offset = !empty($pagination->offset) ? $pagination->offset : 0;
        $limit = !empty($pagination->limit) ? $pagination->limit : 20;

        return $this->getNonFixedTopics()->offset($offset)->limit($limit);
    }

    /**
     * @return \yii\db\ActiveQuery|null
     */
    public function getLastPost()
    {
        return $this->hasOne(Post::className(), ['id' => 'last_post_id']);
    }

    /**
     * @inheritdoc
     */
    public static function queryClassName()
    {
        return SubforumQuery::className();
    }
}
