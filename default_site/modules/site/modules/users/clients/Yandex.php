<?php

namespace site\modules\users\clients;

use Yii;
use yii\authclient\clients\YandexOAuth as BaseYandex;
use app\helpers\ModuleHelper;

class Yandex extends BaseYandex implements ClientInterface
{
    /** @inheritdoc */
    public function getEmail()
    {
        $emails = isset($this->getUserAttributes()['emails'])
            ? $this->getUserAttributes()['emails']
            : null;

        if ($emails !== null && isset($emails[0])) {
            return $emails[0];
        } else {
            return null;
        }
    }

    /** @inheritdoc */
    public function getUsername()
    {
        return isset($this->getUserAttributes()['login'])
            ? $this->getUserAttributes()['login']
            : null;
    }

    /** @inheritdoc */
    protected function defaultTitle()
    {
        return Yii::t(ModuleHelper::USERS, 'Yandex');
    }
}
