<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 11.04.16 14:44
 */

use yii\helpers\Url;
use app\helpers\RouteHelper;
use tests\codeception\functional\AdminTester;
use tests\codeception\_pages\site\users\LoginPage;
use design\modules\menu\models\MenuItem;

/* @var $scenario Codeception\Scenario */
$I = new AdminTester($scenario);
$I->wantTo('Ensure repositioning for menu items works');

$I->amGoingTo('log in as admin');
LoginPage::openBy($I)->login($I->credentials['username'], $I->credentials['password']);

$I->amGoingTo('check position of menu items before action');
$I->seeRecord(MenuItem::className(), ['label' => 'Yandex', 'position' => '0']);
$I->seeRecord(MenuItem::className(), ['label' => 'Google', 'position' => '1']);
$I->seeRecord(MenuItem::className(), ['label' => 'Yahoo', 'position' => '2']);

$I->amGoingTo('send request to change menu items position');
$I->sendAjaxPostRequest(Url::to([RouteHelper::ADMIN_DESIGN_MENU_ITEMS_POSITION]), ['old' => 0, 'new' => 2]);

$I->amGoingTo('check position of menu items after action');
$I->seeRecord(MenuItem::className(), ['label' => 'Yandex', 'position' => '2']);
$I->seeRecord(MenuItem::className(), ['label' => 'Google', 'position' => '0']);
$I->seeRecord(MenuItem::className(), ['label' => 'Yahoo', 'position' => '1']);

$I->amGoingTo('send request to change menu items position');
$I->sendAjaxPostRequest(Url::to([RouteHelper::ADMIN_DESIGN_MENU_ITEMS_POSITION]), ['old' => 1, 'new' => 0]);

$I->amGoingTo('check position of menu items after action');
$I->seeRecord(MenuItem::className(), ['label' => 'Yandex', 'position' => '2']);
$I->seeRecord(MenuItem::className(), ['label' => 'Google', 'position' => '1']);
$I->seeRecord(MenuItem::className(), ['label' => 'Yahoo', 'position' => '0']);
