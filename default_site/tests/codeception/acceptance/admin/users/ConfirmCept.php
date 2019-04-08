<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 20.03.16 9:17
 */

use tests\codeception\_pages\site\users\LoginPage;
use tests\codeception\acceptance\UserTester;
use tests\codeception\acceptance\AdminTester;
use tests\codeception\_pages\admin\AdminPage;

$I = new AdminTester($scenario);
$I->wantTo('confirm user registration');

$I->amGoingTo('call user to try login without confirm');
$userTester = $I->haveFriend('user', 'tests\codeception\acceptance\UserTester');
$userTester->does(function(UserTester $I) {
    $I->amOnPage(Yii::$app->homeUrl);
    $I->amNotConfirmed();
    $I->amGoingTo('login without register confirmation');
    $I->click(LoginPage::LOGIN_LINK);
    $loginPage = LoginPage::openedBy($I);
    $loginPage->login($I->credentials['username'], $I->credentials['password']);
    try {
        $I->dontSee('confirm your email address');
    } catch (\Exception $e) {
        return;
    }
    $I->fail('looks like I can login without confirmed registration');
});

$I->amGoingTo('confirm user');
$I->amOnPage(Yii::$app->homeUrl);
$I->amLogoutIfLoggedIn();

$I->amGoingTo('open admin panel');
AdminPage::openBy($I);

$I->amGoingTo('open users management page');
$I->click('Users management');

$I->amGoingTo('click confirm user button');
$I->clickViaPostCsrf('tr[data-key="3"] a[data-confirm*="confirm this user"]');
$I->see('User has been confirmed');

$I->amGoingTo('call user to try login with confirmed registration');
$userTester->does(function(UserTester $I) {
    $I->amOnPage(Yii::$app->homeUrl);
    $I->amNotConfirmed();
    $I->amLogoutIfLoggedIn();
    $I->amGoingTo('login with confirmed registration');
    $I->click(LoginPage::LOGIN_LINK);
    $loginPage = LoginPage::openedBy($I);
    $loginPage->login($I->credentials['username'], $I->credentials['password']);
    $I->dontSee('confirm your email address');
    $I->see('Logout');
});
