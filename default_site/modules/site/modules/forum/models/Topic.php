<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 30.04.2016 10:43
 * via Gii Model Generator
 */

namespace site\modules\forum\models;

use Yii;
use yii\helpers\StringHelper;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\SluggableBehavior;
use yii\data\Pagination;
use yii\db\BaseActiveRecord;
use app\helpers\ModuleHelper;
use yii\tools\behaviors\CountableBehavior;
use site\modules\forum\models\query\TopicQuery;

/**
 * This is the model class for table "{{%forum_topics}}".
 *
 * @property integer $id
 * @property integer $subforum_id
 * @property string $title
 * @property string $slug
 * @property string $description
 * @property integer $views_num
 * @property integer $posts_num
 * @property integer $is_closed
 * @property integer $is_fixed
 * @property integer $last_post_id
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $created_at
 * @property integer $updated_at
 */
class Topic extends Entity
{
    const POST_PER_PAGE = 10;

    /**
     * @var string
     */
    private $content;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%forum_topics}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array_merge(
            [
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
                    'counterParam' => 'forum_topics_count',
                ],
            ],
            array_replace_recursive(parent::behaviors(), [
                'secure' => [
                    'secureRoles' => [],//[RbacHelper::FORUM_TOPICS_ACCESS_CONTROL],
                    'secureItemTemplate' => 'ACCESS_FORUM_TOPICS',
                    'secureItemDescription' => Yii::t(
                        ModuleHelper::ADMIN_FORUM,
                        'Access to forum topic "{secureItemDescriptionParam}"'
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
            static::SCENARIO_CREATE => ['subforum_id', 'title', 'content', 'description', 'is_closed', 'is_fixed'],
            static::SCENARIO_UPDATE => ['title', 'content', 'description', 'is_closed', 'is_fixed'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'subforum_id', 'created_by', 'created_at', 'updated_at'], 'required'],
            [['subforum_id', 'views_num', 'posts_num', 'last_post_id',
                'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['is_closed', 'is_fixed'], 'boolean'],
            [['title', 'slug'], 'string', 'max' => 255],
            [['description'], 'string', 'max' => 400],
            [['slug'], 'unique'],
            // relations
            [['content'], 'required'],
            [['content'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'subforum_id' => Yii::t(ModuleHelper::FORUM, 'Subforum ID'),
            'title' => Yii::t('app', 'Title'),
            'slug' => Yii::t('app', 'Slug'),
            'description' => Yii::t('app', 'Description'),
            'views_num' => Yii::t(ModuleHelper::FORUM, 'Views'),
            'posts_num' => Yii::t(ModuleHelper::FORUM, 'Posts'),
            'is_closed' => Yii::t(ModuleHelper::FORUM, 'Is Closed'),
            'is_fixed' => Yii::t(ModuleHelper::FORUM, 'Is Fixed'),
            'last_post_id' => Yii::t(ModuleHelper::FORUM, 'Last Post ID'),
            'rbac_on' => Yii::t('app', 'Access control'),
            'rbac_item' => Yii::t('app', 'Access permission'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            // relations
            'content' => Yii::t('app', 'Message text'),
        ];
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            $firstPost = Yii::createObject([
                'class' => Post::className(),
                'topic_id' => $this->id,
                'content' => $this->content,
                'is_first' => 1,
            ]);
            $firstPost->save(false);
        } else {
            if (($firstPost = $this->firstPost) && isset($this->content)) {
                $firstPost->content = $this->content;
                $firstPost->save(false);
            }
        }

        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @return string
     */
    public function getShortTitle()
    {
        return StringHelper::truncate($this->title, 18);
    }

    /**
     * Do not use this method directly, use 'lastPost' relation instead.
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * Do not use this method directly, use 'lastPost' relation instead.
     * @return mixed
     */
    public function getContent()
    {
        if (!isset($this->content)) {
            if ($firstPost = $this->firstPost) {
                $this->content = $firstPost->content;
            }
        }

        return $this->content;
    }

    /**
     * Returns subforum that contains this topic.
     * @return \yii\db\ActiveQuery|null
     */
    public function getSubforum()
    {
        return $this->hasOne(Subforum::className(), ['id' => 'subforum_id'])->secure(true);
    }

    /**
     * Returns all posts that belongs to this topic.
     * @return \yii\db\ActiveQuery|null
     */
    public function getPosts()
    {
        return $this->hasMany(Post::className(), ['topic_id' => 'id'])->secure(true)->inverseOf('topic');
    }

    /**
     * Returns total count of all posts that belongs to this topic.
     * @return int
     */
    public function getPostsCount()
    {
        return $this->getPosts()->count();
    }

    /**
     * @return int
     */
    public function getPostsPerPage()
    {
        return static::POST_PER_PAGE;
    }

    /**
     * Returns number of the last page of this topic.
     * @return int
     */
    public function getLastPage()
    {
        return ceil($this->postsCount / $this->postsPerPage);
    }

    /**
     * Returns posts that belongs to this topic in context of pagination.
     * @return \yii\db\ActiveQuery|null
     */
    public function getPostsOnPage()
    {
        /** @var Pagination $pagination */
        $pagination = Yii::$container->get(Pagination::className());

        $offset = !empty($pagination->offset) ? $pagination->offset : 0;
        $limit = !empty($pagination->limit) ? $pagination->limit : Topic::POST_PER_PAGE;

        return $this->getPosts()->offset($offset)->limit($limit);
    }

    /**
     * Returns topic's content post (first post).
     * @return \yii\db\ActiveQuery|null
     */
    public function getFirstPost()
    {
        return $this->hasOne(Post::className(), ['topic_id' => 'id'])->andWhere(['=', 'is_first', 1]);
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
        return TopicQuery::className();
    }
}
