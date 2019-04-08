<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 21.03.16 6:15
 */

use tests\codeception\acceptance\AdminTester;
use tests\codeception\_pages\admin\AdminPage;

$I = new AdminTester($scenario);
$I->wantTo('view permissions');

$I->amGoingTo('enter admin panel');
AdminPage::openBy($I);

$I->amGoingTo('open permissions view page');
$I->click('Permissions overview');
$I->see('ADMIN_ACCESS');

$I->amGoingTo('check permissions on page 3');
$I->click('.tab-content .pagination a[data-page="2"]');
$I->see('NEWS_DELETE');
