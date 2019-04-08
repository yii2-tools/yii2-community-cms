<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 08.04.16 8:20
 */

namespace tests\codeception\_pages\admin\design\packs;

use tests\codeception\_pages\LoginRequiredPage;
use app\helpers\RouteHelper;

/**
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class OverviewPage extends LoginRequiredPage
{
    public $route = [RouteHelper::ADMIN_DESIGN];

    public function import($file)
    {
        $I = $this->actor;

        $I->attachFile('.tab-content form input[type="file"]', $file);
        $I->click('.tab-content form button[type="submit"]');
    }
}