<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 29.04.16 15:06
 */

use tests\codeception\acceptance\AdminTester;
use tests\codeception\_pages\admin\AdminPage;
use tests\codeception\_pages\admin\pages\CreatePage;

$I = new AdminTester($scenario);
$I->wantTo('create public page');

$I->amGoingTo("ensure that page doesn't exists");
$url = Yii::$app->getUrlManager()->createUrl('new-public-page');
$I->amOnPage($url);
$I->dontSee('Content of public custom page here...');
$I->see('404');

$I->amGoingTo('enter admin panel');
AdminPage::openBy($I);

$I->amGoingTo('open pages management page');
$I->click('Pages management');

$I->amGoingTo('click create page link');
$I->click(".nav-tabs a:contains(Create)");
$createPage = CreatePage::openedBy($I);

$I->amGoingTo('create new public page');
$createPage->create('New public page', 'Content of public custom page here...', 'new-public-page');
$I->see('successfully created');

$I->amGoingTo('see new public page');
$I->amOnPage($url);
$I->see('Content of public custom page here...');
