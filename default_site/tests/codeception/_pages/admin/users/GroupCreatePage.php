<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 20.03.16 12:36
 */

namespace tests\codeception\_pages\admin\users;

use tests\codeception\_pages\LoginRequiredPage;
use app\helpers\RouteHelper;

/**
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class GroupCreatePage extends LoginRequiredPage
{
    public $route = [RouteHelper::ADMIN_USERS_ROLES_CREATE];

    public function create($name = '', $description = '', $children = [])
    {
        $I = $this->actor;

        $I->submitFormCsrf('.tab-content form', [
            'RoleForm[name]' => $name,
            'RoleForm[description]' => $description,
            'RoleForm[children]' => $children,
        ]);
    }
}