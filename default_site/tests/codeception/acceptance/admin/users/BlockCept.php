<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 20.03.16 10:54
 */

use tests\codeception\_pages\site\users\LoginPage;
use tests\codeception\acceptance\UserTester;
use tests\codeception\acceptance\AdminTester;
use tests\codeception\_pages\admin\AdminPage;

$I = new AdminTester($scenario);
$I->wantTo('block user');

$I->amGoingTo('open admin panel');
AdminPage::openBy($I);

$I->amGoingTo('open users management page');
$I->click('Users management');

$I->amGoingTo('click confirm user button');
$I->clickViaPostCsrf('tr[data-key="2"] a[data-confirm*="block this user"]');
$I->see('User has been blocked');

$I->amGoingTo('look at user which no longer can login');
$userTester = $I->haveFriend('user', 'tests\codeception\acceptance\UserTester');
$userTester->does(function(UserTester $I) {
    $I->amOnPage(Yii::$app->homeUrl);
    $I->amLogoutIfLoggedIn();
    $I->amGoingTo('login with blocked account');
    $I->click(LoginPage::LOGIN_LINK);
    $loginPage = LoginPage::openedBy($I);
    $loginPage->login($I->credentials['username'], $I->credentials['password']);
    $I->see('account has been blocked');
    $I->dontSee('Logout');
});
