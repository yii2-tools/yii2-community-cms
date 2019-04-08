<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 16.03.16 18:17
 */

use tests\codeception\acceptance\UserTester;
use tests\codeception\_pages\site\users\RegisterPage;

$I = new UserTester($scenario);
$I->wantTo('register');

$I->amGoingTo('open registration page');
$registerPage = RegisterPage::openBy($I);

$registerPage->register('tester', 'tester@domain.ltd', '123456', '123456');
$I->see('Your account has been created');

$I->amGoingTo('finish registration by opening confirmation link');
$confirmationLink = $I->grabLinkFromLastEmail();
$I->amOnUrl($confirmationLink);

$I->dontSee('confirmation link is invalid or expired');
$I->see('registration is now complete');
$I->expectTo('be auto authorized after registration confirmed, that would be nice');
$I->see('Logout');
