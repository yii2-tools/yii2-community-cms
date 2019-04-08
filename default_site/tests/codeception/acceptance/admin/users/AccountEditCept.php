<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 18.03.16 17:04
 */

use tests\codeception\_pages\site\users\LoginPage;
use tests\codeception\acceptance\UserTester;
use tests\codeception\acceptance\AdminTester;
use tests\codeception\_pages\admin\AdminPage;
use tests\codeception\_pages\admin\users\AccountEditPage;

$I = new AdminTester($scenario);
$I->wantTo('change user account');
AdminPage::openBy($I);

$I->amGoingTo('open users management page');
$I->click('Users management');

$I->amGoingTo('open user edit page');
$I->click('tr[data-key="2"] a[title="Update"]');
$accountEditPage = AccountEditPage::openedBy($I);

$I->amGoingTo('change user private settings');
$accountEditPage->edit('userChanged@domain.ltd', 'userChanged', '112233');
$I->see('successfully updated');
$I->click('.nav-tabs a:contains(Overview)');
$I->see('userChanged');
$I->see('userChanged@domain.ltd');

$I->amGoingTo('call user and check password is changed');
$userTester = $I->haveFriend('user', 'tests\codeception\acceptance\UserTester');
$userTester->does(function(UserTester $I) {
    $I->amGoingTo('log in with new password');
    $I->amOnPage(Yii::$app->homeUrl);
    $I->amLogoutIfLoggedIn();
    $I->click(LoginPage::LOGIN_LINK);
    $loginPage = LoginPage::openedBy($I);
    $loginPage->login('userChanged', '112233');
    $I->see('Logout');
});