<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 18.03.16 12:54
 */

use tests\codeception\acceptance\UserTester;
use tests\codeception\_pages\site\users\LoginPage;
use tests\codeception\_pages\site\users\RegisterConfirmResendPage;

$I = new UserTester($scenario);
$I->wantTo('resend register confirmation link');
$I->amNotConfirmed();

$I->amGoingTo('log in without confirmation');
$loginPage = LoginPage::openBy($I);
$loginPage->login($I->credentials['username'], $I->credentials['password']);
$I->see('confirm your email address');

$I->amGoingTo('open register confirmation link resend page');
$I->click("Didn't receive confirmation message?");
$confirmResendPage = RegisterConfirmResendPage::openedBy($I);

$I->amGoingTo('request new register confirmation link');
$confirmResendPage->resend($I->email);
$I->see('message has been sent to your email address');

$I->amGoingTo('read last email and open confirmation link');
$link = $I->grabLinkFromLastEmail();
$I->amOnUrl($link);
$I->see('registration is now complete');