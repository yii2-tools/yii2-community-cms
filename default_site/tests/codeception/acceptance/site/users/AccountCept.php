<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 17.03.16 21:06
 */

use tests\codeception\acceptance\UserTester;
use tests\codeception\_pages\site\users\SettingsAccountPage;

/** @var \AcceptanceTester $I */
$I = new UserTester($scenario);
$I->wantTo('change private settings');

$I->amGoingTo('open account settings page');
/** @var SettingsAccountPage $page */
$settingsAccountPage = SettingsAccountPage::openBy($I);

$I->amGoingTo('change account settings (email, password)');
$settingsAccountPage->changeAccount([
    'email' => 'tester2@domain.ltd',
    'newPassword' => '654321',
    'password' => $I->credentials['password']
]);
$I->see('account details have been updated');
$I->see('confirmation links to both old and new email addresses');

$I->amGoingTo('finish changing account settings by opening confirmation links');
$confirmationLinks = $I->grabLinksFromEmails();
$I->amOnUrl($confirmationLinks[0]);
$I->see('you need to click the confirmation link sent to your');
$I->amOnUrl($confirmationLinks[1]);
$I->see('email address has been changed');

$I->amGoingTo('logout and then login again with new password');
$I->click('Logout');
$I->credentials['password'] = '654321';     // new password
SettingsAccountPage::openBy($I);
