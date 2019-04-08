<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 20.03.16 11:02
 */

use tests\codeception\acceptance\UserTester;
use tests\codeception\acceptance\AdminTester;
use tests\codeception\_pages\site\users\LoginPage;
use tests\codeception\_pages\site\users\ProfilePage;
use tests\codeception\_pages\admin\AdminPage;

$I = new AdminTester($scenario);
$I->wantTo('delete user');

$I->amGoingTo('look at user profile');
ProfilePage::openBy($I, ['username' => 'user']);
$I->see('UserUsername');
$I->see('UserLocation');

$I->amGoingTo('enter admin panel');
AdminPage::openBy($I);

$I->amGoingTo('open users management page');
$I->click('Users management');

$I->amGoingTo('click delete user button');
$I->clickViaPostCsrf('tr[data-key="2"] a[data-confirm*="delete this user"]');
$I->see('User has been deleted');

$I->amGoingTo('see what user no longer have account');
$userTester = $I->haveFriend('user', 'tests\codeception\acceptance\UserTester');
$userTester->does(function(UserTester $I) {
    $I->amOnPage(Yii::$app->homeUrl);
    $I->amLogoutIfLoggedIn();
    $I->amGoingTo('login with deleted account');
    $I->click(LoginPage::LOGIN_LINK);
    $loginPage = LoginPage::openedBy($I);
    $loginPage->login($I->credentials['username'], $I->credentials['password']);
    //$I->dontSee('confirm your email address');
    $I->dontSee('Logout');
});