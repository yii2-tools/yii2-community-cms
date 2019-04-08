<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 29.04.16 16:08
 */

use tests\codeception\acceptance\AdminTester;
use tests\codeception\acceptance\UserTester;
use tests\codeception\_pages\admin\AdminPage;
use tests\codeception\_pages\admin\pages\EditPage;
use tests\codeception\_pages\admin\users\AssignmentsEditPage;
use tests\codeception\_pages\site\users\LoginPage;

$I = new AdminTester($scenario);
$I->wantTo('edit custom page');

$I->amGoingTo('see custom page');
$url = Yii::$app->getUrlManager()->createUrl('about');
$I->amOnPage($url);
$I->dontSee('404');
$I->see('Some text here...');

$I->amGoingTo('enter admin panel');
AdminPage::openBy($I);

$I->amGoingTo('open pages management page');
$I->click('Pages management');

$I->amGoingTo('click edit page button');
$I->clickViaPostCsrf('tr[data-key="1"] a[title="Update"]');

// TITLE / CONTENT.
$I->amGoingTo('edit page title and content');
$editPage = EditPage::openedBy($I);
$editPage->edit(['title' => 'Edited title', 'content' => 'Edited content']);
$I->see('successfully updated');

$I->amGoingTo('see custom page after update');
$I->amOnPage($url);
$I->dontSee('404');
$I->see('Edited content');

// URL.
$I->amGoingTo('edit page url');
$editedUrl = Yii::$app->getUrlManager()->createUrl('edited-page-url');
$I->amOnPage($editedUrl);
$I->see('404');
$I->dontSee('Edited content');
EditPage::openBy($I, ['id' => 1])->edit(['route_id' => 'edited-page-url']);
$I->see('successfully updated');
$I->amOnPage($url);
$I->see('404');
$I->dontSee('Edited content');
$I->amOnPage($editedUrl);
$I->dontSee('404');
$I->see('Edited content');

// Make private.
$I->amGoingTo('look at guest which can see page without access control');
$userTester = $I->haveFriend('guest', 'tests\codeception\acceptance\UserTester');
$userTester->does(function(UserTester $I) use ($editedUrl) {
    $I->amOnPage(Yii::$app->homeUrl);
    $I->amLogoutIfLoggedIn();
    $I->amOnPage($editedUrl);
    $I->see('Edited content');
});

$I->amGoingTo('edit page access control: make page private');
$I->amLogoutIfLoggedIn();
EditPage::openBy($I, ['id' => 1])->edit([
    'route_id' => 'edited-page-url',
    'rbac_on' => 1,
    'secureAccessRoles' => ['User']
]);
$I->see('successfully updated');

$I->amGoingTo('look at user which cannot anymore see page without access control');
$userTester->does(function(UserTester $I) use ($editedUrl) {
    $I->amOnPage(Yii::$app->homeUrl);
    $I->amLogoutIfLoggedIn();
    $I->amOnPage($editedUrl);
    $I->see('404');
    $I->dontSee('Edited content');
    LoginPage::openBy($I)->login($I->credentials['username'], $I->credentials['password']);
    $I->amOnPage($editedUrl);
    $I->see('404');
    $I->dontSee('Edited content');
});

$I->amGoingTo('add user to group with permission to see private page');
$I->amLogoutIfLoggedIn();
AssignmentsEditPage::openBy($I, ['id' => 2])->edit(['User']);

$I->amGoingTo('look at user which already can see page with granted permission');
$userTester->does(function(UserTester $I) use ($editedUrl) {
    $I->amOnPage(Yii::$app->homeUrl);
    $I->amLogoutIfLoggedIn();
    $I->amOnPage($editedUrl);
    $I->see('404');
    $I->dontSee('Edited content');
    LoginPage::openBy($I)->login($I->credentials['username'], $I->credentials['password']);
    $I->amOnPage($editedUrl);
    $I->dontSee('404');
    $I->see('Edited content');
});

$I->amGoingTo('edit page access control: make page private');
$I->amLogoutIfLoggedIn();
EditPage::openBy($I, ['id' => 1])->edit(['route_id' => 'edited-page-url', 'rbac_on' => 0]);
$I->see('successfully updated');

$I->amGoingTo('remove user from group with permission to see private page');
AssignmentsEditPage::openBy($I, ['id' => 2])->edit([]);

$I->amGoingTo('look at user which can see page with public access');
$userTester->does(function(UserTester $I) use ($editedUrl) {
    $I->amOnPage(Yii::$app->homeUrl);
    $I->amLogoutIfLoggedIn();
    $I->amOnPage($editedUrl);
    $I->dontSee('404');
    $I->see('Edited content');
    LoginPage::openBy($I)->login($I->credentials['username'], $I->credentials['password']);
    $I->amOnPage($editedUrl);
    $I->dontSee('404');
    $I->see('Edited content');
});