<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 08.04.16 10:17
 */

namespace tests\codeception\_pages\admin\design;

use tests\codeception\_pages\LoginRequiredPage;
use tests\codeception\_pages\traits\ParameterizedPage;
use app\helpers\RouteHelper;

/**
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class SetupPage extends LoginRequiredPage
{
    public $route = [RouteHelper::ADMIN_SETUP, 'module' => 'design'];

    public function change($params = [])
    {
        $I = $this->actor;

        foreach ($params as $index => $value) {
            $I->selectOption('select[name="ActiveParam[' . $index . '][value]"]', $value);
        }

        $I->click('.tab-content form button[type="submit"]');
    }
}
