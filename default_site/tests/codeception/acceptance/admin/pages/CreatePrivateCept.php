<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 29.04.16 15:24
 */

use tests\codeception\acceptance\AdminTester;
use tests\codeception\acceptance\UserTester;
use tests\codeception\_pages\admin\AdminPage;
use tests\codeception\_pages\admin\pages\CreatePage;
use tests\codeception\_pages\site\users\LoginPage;
use tests\codeception\_pages\admin\users\AssignmentsEditPage;
use tests\codeception\fixtures\UserFixture;

$I = new AdminTester($scenario);
$I->wantTo('create private page');

$I->amGoingTo("ensure that page doesn't exists");
$url = Yii::$app->getUrlManager()->createUrl('new-private-page');
$I->amOnPage($url);
$I->dontSee('Content of private custom page here...');
$I->see('404');

$I->amGoingTo('enter admin panel');
AdminPage::openBy($I);

$I->amGoingTo('open pages management page');
$I->click('Pages management');

$I->amGoingTo('click create page link');
$I->click(".nav-tabs a:contains(Create)");
$createPage = CreatePage::openedBy($I);

$I->amGoingTo('create new private page');
$createPage->create('New private page', 'Content of private custom page here...', 'new-private-page', 1, ['Member']);
$I->see('successfully created');

$I->amGoingTo('see new private page as admin');
$I->amOnPage($url);
$I->see('Content of private custom page here...');

$I->amGoingTo('add user to group with permission to see private page');
$assignmentsEditPage = AssignmentsEditPage::openBy($I, ['id' => 2]);
$assignmentsEditPage->edit(['Member']);
$assignmentsEditPage = AssignmentsEditPage::openBy($I, ['id' => 4]);
$assignmentsEditPage->edit(['User']);

$I->amGoingTo('look at guest which trying to see private page');
$userTester = $I->haveFriend('guest', 'tests\codeception\acceptance\UserTester');
$userTester->does(function(UserTester $I) use ($url) {
    $I->amOnPage(Yii::$app->homeUrl);
    $I->amLogoutIfLoggedIn();
    $I->amOnPage($url);
    $I->dontSee('Content of private custom page here...');
    $I->see('404');
    LoginPage::openBy($I)->login('filledProfileUser', UserFixture::PASSWORD);
    $I->amOnPage($url);
    $I->see('404');
    $I->dontSee('Content of private custom page here...');
});

$I->amGoingTo('look at user which can see private page');
$userTester = $I->haveFriend('user', 'tests\codeception\acceptance\UserTester');
$userTester->does(function(UserTester $I) use ($url) {
    $I->amOnPage(Yii::$app->homeUrl);
    $I->amLogoutIfLoggedIn();
    LoginPage::openBy($I)->login($I->credentials['username'], $I->credentials['password']);
    $I->amOnPage($url);
    $I->dontSee('404');
    $I->see('Content of private custom page here...');
});
