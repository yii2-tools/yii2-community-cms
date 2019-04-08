<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 11.04.16 14:44
 */

use tests\codeception\acceptance\AdminTester;
use tests\codeception\_pages\admin\AdminPage;
use tests\codeception\_pages\admin\design\menu\items\EditPage;

$I = new AdminTester($scenario);
$I->wantTo('edit menu item');
$I->seeLink('Yahoo');

$I->amGoingTo('enter admin panel');
AdminPage::openBy($I);

$I->amGoingTo('open menu management page');
$I->click('Menu management');

$I->amGoingTo('open edit menu item page');
$I->clickViaPostCsrf("a[href*='update?id=3']");
$createPage = EditPage::openedBy($I);
$createPage->update(['label' => 'Bing', 'url' => 'http://bing.com', 'is_route' => false]);
$I->amOnPage(Yii::$app->homeUrl);
$I->dontSeeLink('Yahoo');
$I->seeLink('Bing');
$I->seeInSource('http://bing.com');
