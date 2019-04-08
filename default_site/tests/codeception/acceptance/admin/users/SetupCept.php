<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 21.03.16 6:28
 */

use tests\codeception\acceptance\AdminTester;
use tests\codeception\_pages\admin\AdminPage;
use tests\codeception\_pages\admin\users\SetupPage;

$I = new AdminTester($scenario);
$I->wantTo('setup users module');

$I->amGoingTo('enter admin panel');
AdminPage::openBy($I);

$I->amGoingTo('open setup users module page');
$setupPage = SetupPage::openBy($I);

$I->amGoingTo('change default groups for user and guest');
$setupPage->change(['Member', 'Reserve event master']);
$I->see('successfully updated');
