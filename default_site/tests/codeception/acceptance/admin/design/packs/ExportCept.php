<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 08.04.16 20:12
 */

use tests\codeception\acceptance\AdminTester;
use tests\codeception\_pages\admin\AdminPage;

$I = new AdminTester($scenario);
$I->wantTo('export design pack');

$I->amGoingTo('enter admin panel');
AdminPage::openBy($I);

$I->amGoingTo('open design packs management page');
$I->click('Design packs management');

$I->amGoingTo('download design pack');
$I->clickViaPostCsrf("a[title='Export']", ['name' => 'custom']);
$I->seeInSource('/templates/main.twig');
