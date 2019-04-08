<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 19.03.16 16:15
 */

namespace tests\codeception\_pages\admin\users;

use tests\codeception\_pages\LoginRequiredPage;
use app\helpers\RouteHelper;
use tests\codeception\_pages\traits\ParameterizedPage;

/**
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class AccountEditPage extends LoginRequiredPage
{
    use ParameterizedPage;

    public static $params = ['id'];
    public $route = [RouteHelper::ADMIN_USERS_MANAGEMENT_UPDATE];

    public function edit($email = null, $username = null, $password = null)
    {
        $I = $this->actor;

        if (isset($email)) {
            $I->fillField('input[name="User[email]"]', $email);
        }

        if (isset($username)) {
            $I->fillField('input[name="User[username]"]', $username);
        }

        if (isset($password)) {
            $I->fillField('input[name="User[password]"]', $password);
        }

        $I->click('.tab-content form button[type="submit"]');
    }
}