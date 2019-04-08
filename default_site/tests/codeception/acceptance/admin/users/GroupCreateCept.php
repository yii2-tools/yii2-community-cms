<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 20.03.16 12:23
 */

use tests\codeception\acceptance\AdminTester;
use tests\codeception\_pages\admin\AdminPage;
use tests\codeception\_pages\admin\users\GroupCreatePage;
use tests\codeception\_pages\admin\users\GroupEditPage;

$I = new AdminTester($scenario);
$I->wantTo('create group');

$I->amGoingTo('open admin panel');
AdminPage::openBy($I);

$I->amGoingTo('open roles management page');
$I->click('Roles management');
$I->dontSee('New role');

$I->amGoingTo('open create group page');
$I->click(".nav-tabs .dropdown-menu a:contains(Role)");
$groupCreatePage = GroupCreatePage::openedBy($I);

$I->amGoingTo('create new group');
$groupCreatePage->create('New role', 'This is new role', ['PLUGINS_EVENTS_ACCESS', 'Team leader']);
$I->see('successfully created');
$I->see('New role');
$I->see('This is new role');
