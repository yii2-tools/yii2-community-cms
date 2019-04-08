<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 11.04.16 14:44
 */

use tests\codeception\_pages\site\users\RegisterPage;
use tests\codeception\acceptance\AdminTester;
use tests\codeception\_pages\admin\AdminPage;
use tests\codeception\_pages\admin\design\menu\items\CreatePage;

$I = new AdminTester($scenario);
$I->wantTo('add menu item');
$I->dontSeeLink('Registration page menu item');

$I->amGoingTo('enter admin panel');
AdminPage::openBy($I);

$I->amGoingTo('open menu management page');
$I->click('Menu management');

$I->amGoingTo('add new menu item');
$I->click(".tab-content form button[type='submit']");
$createPage = CreatePage::openedBy($I);
$createPage->create(['label' => 'Registration page menu item', 'is_route' => true, 'route_id' => 2200]);
$I->seeLink('Registration page menu item');

$I->amGoingTo('click on new menu item');
$I->amLogoutIfLoggedIn();
$I->click('Registration page menu item');
$registerPage = RegisterPage::openedBy($I);
$I->see('Sign up');
