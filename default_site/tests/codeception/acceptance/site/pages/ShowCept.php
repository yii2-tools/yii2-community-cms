<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 29.04.16 15:00
 */

use tests\codeception\acceptance\UserTester;

$I = new UserTester($scenario);
$I->wantTo('see custom page');

$url = Yii::$app->getUrlManager()->createUrl('about');
$I->amOnPage($url);
$I->see('Some text here...');
