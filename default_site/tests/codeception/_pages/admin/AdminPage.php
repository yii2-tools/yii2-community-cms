<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 18.03.16 17:09
 */

namespace tests\codeception\_pages\admin;

use tests\codeception\_pages\LoginRequiredPage;
use app\helpers\RouteHelper;

/**
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class AdminPage extends LoginRequiredPage
{
    public $route = [RouteHelper::ADMIN_HOME];
}