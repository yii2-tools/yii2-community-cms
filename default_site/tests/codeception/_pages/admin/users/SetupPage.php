<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 21.03.16 6:29
 */

namespace tests\codeception\_pages\admin\users;

use tests\codeception\_pages\LoginRequiredPage;
use tests\codeception\_pages\traits\ParameterizedPage;
use app\helpers\RouteHelper;

/**
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class SetupPage extends LoginRequiredPage
{
    public $route = [RouteHelper::ADMIN_SETUP, 'module' => 'users'];

    public function change($params = [])
    {
        $I = $this->actor;

        $I->selectOption('select[name="ActiveParam[0][value]"]', $params[0]);
        $I->selectOption('select[name="ActiveParam[1][value]"]', $params[1]);

        $I->click('.tab-content form button[type="submit"]');
    }
}
