<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 19.03.16 19:48
 */

/*
 * Note: if this test failed, maybe reason is bad names of roles and permissions
 * (look at ->edit() permissions call, params array)
 */

use tests\codeception\acceptance\UserTester;
use tests\codeception\acceptance\AdminTester;
use tests\codeception\_pages\admin\AdminPage;
use tests\codeception\_pages\admin\users\AssignmentsEditPage;

$I = new AdminTester($scenario);
$I->wantTo('change user permissions');

$I->amGoingTo('call user to try enter admin panel without permission');
$userTester = $I->haveFriend('user', 'tests\codeception\acceptance\UserTester');
$userTester->does(function(UserTester $I) {
    $I->amGoingTo('enter admin area without permission');
    try {
        AdminPage::openBy($I);
    } catch (\Exception $e) {
        if (!AdminPage::accessError()) {
            throw $e;
        }
        return;
    }
    $I->fail('looks like I have access to admin area without permission');
});

$I->amGoingTo('add permission to access admin panel for user');
$I->amOnPage(Yii::$app->homeUrl);
$I->amLogoutIfLoggedIn();

$I->amGoingTo('open admin panel');
AdminPage::openBy($I);

$I->amGoingTo('open users management page');
$I->click('Users management');

$I->amGoingTo('open user edit page');
$I->click('tr[data-key="2"] a[title="Update"]');

$I->amGoingTo('open user permissions assign page');
$I->click('Assignments');
$assignmentsEditPage = AssignmentsEditPage::openedBy($I);

$I->amGoingTo('edit user permissions');
$assignmentsEditPage->edit(['NEWS_MODERATOR', 'ADMIN_ACCESS', 'Vendor']);

$I->amGoingTo('call user to try enter admin panel when access permission properly assigned');
$userTester->does(function(UserTester $I) {
    $I->amGoingTo('enter admin area with valid permission');
    $I->amOnPage(Yii::$app->homeUrl);
    $I->amLogoutIfLoggedIn();
    try {
        AdminPage::openBy($I);
    } catch (\Exception $e) {
        if (!AdminPage::accessError()) {
            throw $e;
        }
        $I->fail("looks like I still don't have access to admin area");
    }
});