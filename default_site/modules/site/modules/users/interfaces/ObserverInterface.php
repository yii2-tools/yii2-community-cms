<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 01.05.16 22:21
 */

namespace site\modules\users\interfaces;

use yii\web\IdentityInterface;

interface ObserverInterface
{
    /**
     * Updates online status for specified identity.
     * If Identity is not specified (null),
     * current user will be considered as guest.
     * @param IdentityInterface $identity
     */
    public function updateOnlineData($identity = null);

    /**
     * Returns summary count of all users online (guests and authorized).
     * @return int
     */
    public function getOnlineCount();
}
