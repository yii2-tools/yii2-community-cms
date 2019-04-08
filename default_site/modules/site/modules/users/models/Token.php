<?php

namespace site\modules\users\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Url;
use app\helpers\ModuleHelper;
use app\helpers\RouteHelper;

class Token extends ActiveRecord
{
    const TYPE_CONFIRMATION      = 0;
    const TYPE_RECOVERY          = 1;
    const TYPE_CONFIRM_NEW_EMAIL = 2;
    const TYPE_CONFIRM_OLD_EMAIL = 3;

    protected $module;

    /** @inheritdoc */
    public function init()
    {
        $this->module = Yii::$app->getModule(ModuleHelper::USERS);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        switch ($this->type) {
            case self::TYPE_CONFIRMATION:
                $route = RouteHelper::SITE_USERS_REGISTRATION_CONFIRM;
                break;
            case self::TYPE_RECOVERY:
                $route = RouteHelper::SITE_USERS_RECOVERY_RESET;
                break;
            case self::TYPE_CONFIRM_NEW_EMAIL:
            case self::TYPE_CONFIRM_OLD_EMAIL:
                $route = RouteHelper::SITE_USERS_SETTINGS_CONFIRM;
                break;
            default:
                throw new \RuntimeException();
        }

        $key = base64_encode(http_build_query([
            'id' => $this->user_id,
            'code' => $this->code
        ]));

        return Url::to([$route, 'key' => $key], true);
    }

    /**
     * @return bool Whether token has expired.
     */
    public function getIsExpired()
    {
        switch ($this->type) {
            case self::TYPE_CONFIRMATION:
            case self::TYPE_CONFIRM_NEW_EMAIL:
            case self::TYPE_CONFIRM_OLD_EMAIL:
                $expirationTime = $this->module->confirmWithin;
                break;
            case self::TYPE_RECOVERY:
                $expirationTime = $this->module->recoverWithin;
                break;
            default:
                throw new \RuntimeException();
        }

        return ($this->created_at + $expirationTime) < time();
    }

    /** @inheritdoc */
    public function beforeSave($insert)
    {
        if ($insert) {
            static::deleteAll(['user_id' => $this->user_id, 'type' => $this->type]);
            $this->setAttribute('created_at', time());
            $this->setAttribute('code', Yii::$app->security->generateRandomString());
        }

        return parent::beforeSave($insert);
    }

    /** @inheritdoc */
    public static function tableName()
    {
        return '{{%users_tokens}}';
    }

    /** @inheritdoc */
    public static function primaryKey()
    {
        return ['user_id', 'code', 'type'];
    }
}
