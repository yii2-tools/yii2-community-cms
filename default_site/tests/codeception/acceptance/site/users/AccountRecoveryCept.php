<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 18.03.16 15:15
 */

use tests\codeception\acceptance\UserTester;
use tests\codeception\_pages\site\users\LoginPage;
use tests\codeception\_pages\site\users\RecoveryRequestPage;
use tests\codeception\_pages\site\users\RecoveryResetPage;

$I = new UserTester($scenario);
$I->wantTo('recover account');

$I->amGoingTo('open login page and try to log in');
$loginPage = LoginPage::openBy($I);
$loginPage->login($I->credentials['username'], 'forgot password');
$I->see('Invalid login or password');

$I->amGoingTo('open password recovery request page');
$recoveryRequestPage = RecoveryRequestPage::openBy($I);

$I->amGoingTo('request change password');
$recoveryRequestPage->recovery($I->email);
$I->see('email has been sent with instructions for resetting your password');

$I->amGoingTo('read last email and open recovery link');
$link = $I->grabLinkFromLastEmail();
$I->amOnUrl($link);
$I->see('Reset your password');

$I->amGoingTo('create new password');
$I->credentials['password'] = '654321';
$recoveryResetPage = RecoveryResetPage::openedBy($I);
$recoveryResetPage->reset($I->credentials['password'], $I->credentials['password']);
$I->see('password has been changed successfully');

$I->amGoingTo('log in with new password');
$loginPage = LoginPage::openedBy($I);
$loginPage->login($I->credentials['username'], $I->credentials['password']);
$I->see('Logout');
