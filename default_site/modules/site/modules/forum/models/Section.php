<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 30.04.2016 10:42
 * via Gii Model Generator
 */

namespace site\modules\forum\models;

use Yii;
use yii\behaviors\SluggableBehavior;
use app\helpers\ModuleHelper;
use yii\tools\behaviors\PositionBehavior;
use yii\tools\behaviors\CountableBehavior;
use site\modules\forum\models\query\SectionQuery;

/**
 * This is the model class for table "{{%forum_sections}}".
 *
 * @property integer $id
 * @property string $title
 * @property string $slug
 * @property integer $position
 * @property integer $created_at
 * @property integer $updated_at
 */
class Section extends Entity
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%forum_sections}}';
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
                    'counterParam' => 'forum_sections_count',
                ],
            ],
            array_replace_recursive(parent::behaviors(), [
                'secure' => [
                    'secureRoles' => [],//[RbacHelper::FORUM_SECTIONS_ACCESS_CONTROL],
                    'secureItemTemplate' => 'ACCESS_FORUM_SECTIONS',
                    'secureItemDescription' => Yii::t(
                        ModuleHelper::ADMIN_FORUM,
                        'Access to forum section "{secureItemDescriptionParam}"'
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
            static::SCENARIO_CREATE => ['title', 'rbac_on', 'rbac_item'],
            static::SCENARIO_UPDATE => ['title', 'rbac_on', 'rbac_item'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['position', 'created_at', 'updated_at'], 'integer'],
            [['created_at', 'updated_at'], 'required'],
            [['title', 'slug'], 'string', 'max' => 255],
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
            'position' => Yii::t('app', 'Position'),
            'rbac_on' => Yii::t('app', 'Access control'),
            'rbac_item' => Yii::t('app', 'Access permission'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Edited At'),
        ];
    }

    /**
     * Returns all subforums that belongs to this section.
     * @return Subforum[]
     */
    public function getSubforums()
    {
        return $this->hasMany(Subforum::className(), ['section_id' => 'id'])->secure(true);
    }

    /**
     * @inheritdoc
     */
    public static function queryClassName()
    {
        return SectionQuery::className();
    }
}
