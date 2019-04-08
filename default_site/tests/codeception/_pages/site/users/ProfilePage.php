<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 16.03.16 15:13
 */

namespace tests\codeception\_pages\site\users;

use tests\codeception\_pages\Page;
use app\helpers\RouteHelper;

/**
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class ProfilePage extends Page
{
    public $route = [RouteHelper::SITE_USERS_PROFILE_SHOW];
}