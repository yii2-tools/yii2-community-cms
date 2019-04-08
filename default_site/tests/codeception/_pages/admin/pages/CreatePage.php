<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 29.04.16 15:10
 */

namespace tests\codeception\_pages\admin\pages;

use tests\codeception\_pages\LoginRequiredPage;
use app\helpers\RouteHelper;

/**
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class CreatePage extends LoginRequiredPage
{
    public $route = [RouteHelper::ADMIN_PAGES_CREATE];

    public function create($title, $content, $url = '', $accessControl = 0, array $accessRoles = [])
    {
        $I = $this->actor;

        $I->submitFormCsrf('.tab-content form', [
            'Page[title]' => $title,
            'Page[content]' => $content,
            'Page[route_id]' => $url,
            'Page[rbac_on]' => $accessControl,
            'Page[secureAccessRoles]' => $accessRoles,
        ]);
    }
}