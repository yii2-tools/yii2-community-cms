<?php

namespace site\modules\users\models;

use Yii;
use yii\db\ActiveRecord;
use app\helpers\ModuleHelper;

class Profile extends ActiveRecord
{
    protected $module;

    /** @inheritdoc */
    public function init()
    {
        $this->module = Yii::$app->getModule(ModuleHelper::USERS);
    }

    /** @inheritdoc */
    public static function tableName()
    {
        return '{{%users_profiles}}';
    }

    public function scenarios()
    {
        return array_merge(parent::scenarios(), [
            'update' => ['name', 'public_email', 'gravatar_email', 'location', 'website', 'bio'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            'bioString' => ['bio', 'string'],
            'publicEmailPattern' => ['public_email', 'email'],
            'gravatarEmailPattern' => ['gravatar_email', 'email'],
            'websiteUrl' => ['website', 'url'],
            'nameLength' => ['name', 'string', 'max' => 255],
            'publicEmailLength' => ['public_email', 'string', 'max' => 255],
            'gravatarEmailLength' => ['gravatar_email', 'string', 'max' => 255],
            'locationLength' => ['location', 'string', 'max' => 255],
            'websiteLength' => ['website', 'string', 'max' => 255],
            'imageUrlLength' => ['image_url', 'string', 'max' => 255],
        ];
    }

    /** @inheritdoc */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t(ModuleHelper::USERS, 'Name'),
            'public_email' => Yii::t(ModuleHelper::USERS, 'Email (public)'),
            'location' => Yii::t(ModuleHelper::USERS, 'City'),
            'gravatar_email' => Yii::t(ModuleHelper::USERS, 'Gravatar email'),
            'image_url' => Yii::t(ModuleHelper::USERS, 'Avatar'),
            'website' => Yii::t(ModuleHelper::USERS, 'Website'),
            'bio' => Yii::t(ModuleHelper::USERS, 'Bio'),
        ];
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        $username = (!empty($this->name) ? $this->name . ' ' : '') . $this->user->username;

        if (mb_strlen($username, 'UTF-8') > 18) {
            $username = $this->user->username;
        }

        return $username;
    }

    /** @inheritdoc */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isAttributeChanged('gravatar_email')) {
                $this->setAttribute('gravatar_id', md5(strtolower($this->getAttribute('gravatar_email'))));
            }

            return true;
        }

        return false;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if (!$insert) {
            $this->user->touch('updated_at');
        }
    }

    /**
     * @return \yii\db\ActiveQueryInterface
     */
    public function setUser(User $user)
    {
        $this->populateRelation('user', $user);
    }

    /**
     * @return \yii\db\ActiveQueryInterface
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id'])->inverseOf('profile');
    }
}
