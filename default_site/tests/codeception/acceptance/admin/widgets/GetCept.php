<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 23.03.16 11:51
 */

use tests\codeception\acceptance\AdminTester;
use tests\codeception\_pages\admin\AdminPage;

$I = new AdminTester($scenario);
$I->wantTo('get widgets list');

if (!in_array(YII_ENV, [YII_ENV_TEST, YII_ENV_PROD])) {
    $scenario->skip('integration actions available only in test/prod environment');
}

$I->amGoingTo('open admin panel');
AdminPage::openBy($I);

$I->amGoingTo('open widgets page');
$I->click('Widgets management');
$I->see('No widgets available');
$I->dontSee('tr[data-key="0"]');

$I->amGoingTo('click update widgets list button');
$I->click('form[action="widgets/get"] button[type="submit"]');
$I->see('successfully completed');
$I->dontSee('No widgets available');
