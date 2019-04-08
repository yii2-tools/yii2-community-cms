<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 21.03.16 7:15
 */

use tests\codeception\acceptance\AdminTester;
use tests\codeception\_pages\admin\AdminPage;

$I = new AdminTester($scenario);
$I->wantTo('get plugins list');

if (!in_array(YII_ENV, [YII_ENV_TEST, YII_ENV_PROD])) {
    $scenario->skip('integration actions available only in test/prod environment');
}

$I->amGoingTo('open admin panel');
AdminPage::openBy($I);

$I->amGoingTo('open plugins page');
$I->click('Plugins management');
$I->see('No plugins available');
$I->dontSee('tr[data-key="0"]');

$I->amGoingTo('click update plugins list button');
$I->click('form[action="plugins/get"] button[type="submit"]');
$I->see('successfully completed');
$I->dontSee('No plugins available');
