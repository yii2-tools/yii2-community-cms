<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 19.03.16 19:08
 */

use tests\codeception\_pages\site\users\ProfilePage;
use tests\codeception\acceptance\AdminTester;
use tests\codeception\_pages\admin\AdminPage;
use tests\codeception\_pages\admin\users\ProfileEditPage;

$I = new AdminTester($scenario);
$I->wantTo('change user profile');

$I->amGoingTo('look at user profile');
ProfilePage::openBy($I, ['username' => 'user']);
$I->dontSee('ChangedName');
$I->dontSee('ChangedLocation');

$I->amGoingTo('enter admin panel');
AdminPage::openBy($I);

$I->amGoingTo('open users management page');
$I->click('Users management');

$I->amGoingTo('open user edit page');
$I->click('tr[data-key="2"] a[title="Update"]');

$I->amGoingTo('open user profile details');
$I->click('Profile details');
$profileEditPage = ProfileEditPage::openedBy($I);

$I->amGoingTo('change user profile');
$profileEditPage->edit('ChangedName', 'ChangedLocation');
$I->see('successfully updated');

$I->amGoingTo('check user profile');
ProfilePage::openBy($I, ['username' => 'user']);
$I->see('ChangedName');
$I->see('ChangedLocation');
