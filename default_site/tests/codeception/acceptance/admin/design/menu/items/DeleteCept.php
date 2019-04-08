<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 11.04.16 14:44
 */

use tests\codeception\acceptance\AdminTester;
use tests\codeception\_pages\admin\AdminPage;

$I = new AdminTester($scenario);
$I->wantTo('delete menu item');
$I->seeLink('Google');

$I->amGoingTo('enter admin panel');
AdminPage::openBy($I);

$I->amGoingTo('open menu management page');
$I->click('Menu management');

$I->amGoingTo('delete link from menu');
$I->clickViaPostCsrf("a[href*='delete?id=2']");
$I->dontSeeLink('Google');
