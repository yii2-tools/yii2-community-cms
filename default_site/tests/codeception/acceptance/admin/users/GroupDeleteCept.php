<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 21.03.16 6:08
 */

use tests\codeception\acceptance\AdminTester;
use tests\codeception\_pages\admin\AdminPage;

$I = new AdminTester($scenario);
$I->wantTo('delete group');

$I->amGoingTo('open admin panel');
AdminPage::openBy($I);

$I->amGoingTo('open roles management page');
$I->click('Roles management');
$I->see('Event organizer');

$I->amGoingTo('click delete role button');
$I->clickViaPostCsrf('tr[data-key="4"] a[title="Delete"]');
$I->see('successfully removed');
$I->dontSee('Event organizer');
