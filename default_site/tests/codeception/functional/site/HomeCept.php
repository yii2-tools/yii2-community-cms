<?php

/* @var $scenario Codeception\Scenario */

use app\helpers\ModuleHelper;

$I = new FunctionalTester($scenario);
$I->wantTo('ensure that site main page works');

$I->amOnPage(Yii::$app->homeUrl);
$I->see(Yii::$app->getModule(ModuleHelper::DESIGN)->params['copyright']);
$I->see(Yii::$app->params['engine_name']);
