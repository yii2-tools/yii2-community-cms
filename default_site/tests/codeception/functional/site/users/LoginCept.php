<?php

use tests\codeception\_pages\site\users\LoginPage;
use tests\codeception\fixtures\UserFixture;

/* @var $scenario Codeception\Scenario */

$I = new FunctionalTester($scenario);
$I->wantTo('ensure that login works');
$user = $I->getActiveFixture('user')->getModel('user');
$I->amOnPage(Yii::$app->homeUrl);

$loginPage = LoginPage::openBy($I);
$I->see('Password');

$I->amGoingTo('try to login with empty credentials');
$loginPage->login('', '');

$I->expectTo('see validation errors');
$I->see('Login cannot be blank.');
$I->see('Password cannot be blank.');

$I->amGoingTo('try to login with wrong credentials (admin, wrong)');
$loginPage->login($user->username, 'wrong');

$I->expectTo('see what logic or password is invalid');
$I->see('Invalid login or password');

$I->amGoingTo('try to login with valid credentials (admin, 123456)');
$loginPage->login($user->username, UserFixture::PASSWORD);

$I->expectTo('see link logout instead of login');
$I->see('Logout');
