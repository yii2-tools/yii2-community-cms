<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 28.01.16 2:21
 */

namespace site\modules\users\events;

use yii\base\Event;

class AuthAttemptEvent extends Event
{
    /** @var string */
    public $login;
}
