<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 21.03.16 7:30
 */

use tests\codeception\acceptance\AdminTester;

$I = new AdminTester($scenario);
$I->wantTo('deactivate plugin');

$scenario->skip('too complex');
$I->amGoingTo('deactivate plugin');
