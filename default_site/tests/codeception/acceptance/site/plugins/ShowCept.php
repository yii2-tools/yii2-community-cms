<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 18.04.16 19:38
 */

use tests\codeception\acceptance\AdminTester;

$I = new AdminTester($scenario);
$I->wantTo('open plugin page');

$scenario->skip('too complex');
$I->amGoingTo('open plugin page');
