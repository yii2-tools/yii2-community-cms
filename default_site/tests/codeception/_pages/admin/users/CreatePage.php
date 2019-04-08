<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 21.03.16 7:41
 */

namespace tests\codeception\_pages\admin\users;

use tests\codeception\_pages\LoginRequiredPage;
use app\helpers\RouteHelper;

/**
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class CreatePage extends LoginRequiredPage
{
    public $route = [RouteHelper::ADMIN_USERS_MANAGEMENT_CREATE];

    public function create($email, $username, $password)
    {
        $I = $this->actor;

        $I->submitFormCsrf('.tab-content form', [
            'User[email]' => $email,
            'User[username]' => $username,
            'User[password]' => $password,
        ]);
    }
}