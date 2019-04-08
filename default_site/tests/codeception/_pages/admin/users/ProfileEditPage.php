<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 19.03.16 19:10
 */

namespace tests\codeception\_pages\admin\users;

use tests\codeception\_pages\LoginRequiredPage;
use tests\codeception\_pages\traits\ParameterizedPage;
use app\helpers\RouteHelper;

/**
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class ProfileEditPage extends LoginRequiredPage
{
    use ParameterizedPage;

    public static $params = ['id'];
    public $route = [RouteHelper::ADMIN_USERS_MANAGEMENT_UPDATE_PROFILE];

    public function edit($name = null, $location = null)
    {
        $I = $this->actor;

        if (isset($name)) {
            $I->fillField('input[name="Profile[name]"]', $name);
        }

        if (isset($location)) {
            $I->fillField('input[name="Profile[location]"]', $location);
        }

        $I->click('.tab-content form button[type="submit"]');
    }
}