<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 20.03.16 13:01
 */

namespace tests\codeception\_pages\admin\users;

use tests\codeception\_pages\LoginRequiredPage;
use tests\codeception\_pages\traits\ParameterizedPage;
use app\helpers\RouteHelper;

/**
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class GroupEditPage extends LoginRequiredPage
{
    use ParameterizedPage;

    public static $params = ['name'];
    public $route = [RouteHelper::ADMIN_USERS_ROLES_UPDATE];

    public function edit($name = '', $description = '', $children = [])
    {
        $I = $this->actor;

        $I->submitFormCsrf('.tab-content form', [
            'RoleForm[name]' => $name,
            'RoleForm[description]' => $description,
            'RoleForm[children]' => $children,
        ]);
    }
}