<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 16.03.16 18:17
 */

use tests\codeception\acceptance\UserTester;
use tests\codeception\_pages\site\users\ProfilePage;
use tests\codeception\_pages\site\users\SettingsProfilePage;

$I = new UserTester($scenario);
$I->wantTo('change profile');

$I->amGoingTo('look at my empty profile on show page');
ProfilePage::openBy($I, ['username' => $I->credentials['username']]);
$I->dontSee('MyName');
$I->dontSee('MyLocation');
$I->dontSeeInSource('images/user');

$I->amGoingTo('open profile settings page');
/** @var SettingsProfilePage $page */
$page = SettingsProfilePage::openBy($I);

$I->amGoingTo('change fields of my profile');
$page->changeProfile(['name' => 'MyName', 'location' => 'MyLocation', 'image' => 'images/avatar.jpeg']);
$I->see('Your profile has been updated');

$I->amGoingTo('look at my filled profile on show page');
ProfilePage::openBy($I, ['username' => $I->credentials['username']]);
$I->see('MyName');
$I->see('MyLocation');
$I->seeInSource('images/user');