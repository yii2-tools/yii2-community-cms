<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 20.03.16 15:46
 */

use tests\codeception\acceptance\AdminTester;
use tests\codeception\_pages\admin\AdminPage;
use tests\codeception\_pages\admin\users\GroupEditPage;

$I = new AdminTester($scenario);
$I->wantTo('edit group');

$I->amGoingTo('open admin panel');
AdminPage::openBy($I);

$I->amGoingTo('open roles management page');
$I->click('Roles management');
$I->dontSee('Edited role');

$I->amGoingTo('open group edit page');
$I->click('tr[data-key="6"] a[title="Update"]');
$groupEditPage = GroupEditPage::openedBy($I);

$I->amGoingTo('edit this group');
$groupEditPage->edit('Edited role', 'This is edited role', ['Reserve event master', 'FORUM_SELF_POSTS_UPDATE']);
$I->see('successfully updated');
$I->see('Edited role');
$I->see('This is edited role');
$I->click('tr[data-key="6"] a[title="Update"]');
