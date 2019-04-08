<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 21.03.16 7:30
 */

use tests\codeception\acceptance\AdminTester;

$I = new AdminTester($scenario);
$I->wantTo('activate plugin');

$scenario->skip('too complex');
$I->amGoingTo('activate plugin');
