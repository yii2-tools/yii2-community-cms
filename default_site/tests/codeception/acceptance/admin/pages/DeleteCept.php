<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 29.04.16 16:02
 */

use tests\codeception\acceptance\AdminTester;
use tests\codeception\_pages\admin\AdminPage;

$I = new AdminTester($scenario);
$I->wantTo('delete custom page');

$I->amGoingTo('see custom page');
$url = Yii::$app->getUrlManager()->createUrl('about');
$I->amOnPage($url);
$I->dontSee('404');
$I->see('Some text here...');

$I->amGoingTo('enter admin panel');
AdminPage::openBy($I);

$I->amGoingTo('open pages management page');
$I->click('Pages management');

$I->amGoingTo('click delete page button');
$I->clickViaPostCsrf('tr[data-key="1"] a[data-confirm*="delete this page"]');
$I->see('successfully removed');

$I->amGoingTo('ensure custom page removed');
$I->amOnPage($url);
$I->see('404');
$I->dontSee('Some text here...');
