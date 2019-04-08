<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 07.04.16 18:56
 */

use app\modules\services\helpers\ServiceHelper;
use app\modules\services\models\Service;
use tests\codeception\_pages\admin\design\SetupPage;
use tests\codeception\acceptance\AdminTester;
use tests\codeception\_pages\admin\AdminPage;
use tests\codeception\_pages\admin\design\packs\OverviewPage;

$I = new AdminTester($scenario);
$I->wantTo('import/delete a design pack');

$I->amGoingTo('look at old main page template');
$I->dontSee('test design pack template replacing');

$I->amGoingTo('enter admin panel');
AdminPage::openBy($I);

$I->amGoingTo('open design packs management page');
$I->click('Design packs management');
$I->dontSee('test title');

$I->amGoingTo('upload new design pack');
$overviewPage = OverviewPage::openedBy($I);
$overviewPage->import('files/design/packs/test.zip');
$I->see('successfully uploaded');
$I->see('test title');

$I->amGoingTo('activate new design pack');
$I->click('Setup');
$setupPage = SetupPage::openedBy($I);
$setupPage->change([2 => 'test123']);

$I->amGoingTo('look at new main page template');
$I->click('.navbar-brand');
$I->see('test design pack template replacing');

$I->amGoingTo('see default template if I am not paid required service');
Service::deleteAll(['=', 'service_name', ServiceHelper::PACK_BASE]);
$I->amOnPage(Yii::$app->homeUrl);
$I->dontSee('test design pack template replacing');
Service::getDb()->createCommand()
    ->insert(Service::tableName(), ['service_name' => ServiceHelper::PACK_BASE])
    ->execute();
$I->amOnPage(Yii::$app->homeUrl);
$I->see('test design pack template replacing');

$I->amGoingTo('activate an old design pack');
AdminPage::openBy($I);
$I->click('Design packs management');
$I->click('Setup');
$setupPage = SetupPage::openedBy($I);
$setupPage->change([2 => 'custom']);

$I->amGoingTo('delete design pack');
$I->click('Design packs');
$I->clickViaPostCsrf("a[title='Delete']", ['name' => 'test123']);
$I->dontSee('test title');

$I->amGoingTo('look at old main page template');
$I->click('.navbar-brand');
$I->dontSee('test design pack template replacing');
