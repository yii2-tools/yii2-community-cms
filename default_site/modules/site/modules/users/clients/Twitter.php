<?php

namespace site\modules\users\clients;

use yii\helpers\ArrayHelper;
use yii\authclient\clients\Twitter as BaseTwitter;

class Twitter extends BaseTwitter implements ClientInterface
{
    /**
     * @return string
     */
    public function getUsername()
    {
        return ArrayHelper::getValue($this->getUserAttributes(), 'screen_name');
    }

    /**
     * @return null Twitter does not provide user's email address
     */
    public function getEmail()
    {
        return null;
    }
}
