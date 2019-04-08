<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 19.03.16 21:08
 */

namespace tests\codeception\_pages\admin\users;

use tests\codeception\_pages\LoginRequiredPage;
use tests\codeception\_pages\traits\ParameterizedPage;
use app\helpers\RouteHelper;

/**
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class AssignmentsEditPage extends LoginRequiredPage
{
    use ParameterizedPage;

    public static $params = ['id'];
    public $route = [RouteHelper::ADMIN_USERS_MANAGEMENT_ASSIGNMENTS];

    public function edit($items = [])
    {
        $I = $this->actor;

        $I->submitFormCsrf('.tab-content form', [
            'Assignment' => [
                'items' => $items,
            ]
        ]);
    }
}