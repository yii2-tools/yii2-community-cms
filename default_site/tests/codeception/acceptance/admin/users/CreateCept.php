<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 21.03.16 7:34
 */

use tests\codeception\_pages\site\users\ProfilePage;
use tests\codeception\acceptance\AdminTester;
use tests\codeception\_pages\admin\AdminPage;
use tests\codeception\_pages\admin\users\CreatePage;

$I = new AdminTester($scenario);
$I->wantTo('create user');

$I->amGoingTo('ensure what username createdUser not exists');
try {
    ProfilePage::openBy($I, ['username' => 'createdUser']);
    $I->fail('createdUser already exists');
} catch (\Exception $e) {
    if (!ProfilePage::notFoundError()) {
        throw $e;
    }
}

$I->amGoingTo('enter admin panel');
AdminPage::openBy($I);

$I->amGoingTo('open users management page');
$I->click('Users management');

$I->amGoingTo('open user create page');
$I->click(".nav-tabs .dropdown-menu a:contains(User)");
$createPage = CreatePage::openedBy($I);

$I->amGoingTo('create new user');
$createPage->create('createdUser@domain.ltd', 'createdUser', '123456');
$I->see('successfully created');

$I->amGoingTo('check user profile');
ProfilePage::openBy($I, ['username' => 'createdUser']);
$I->see('createdUser');
$I->see('Joined on');
