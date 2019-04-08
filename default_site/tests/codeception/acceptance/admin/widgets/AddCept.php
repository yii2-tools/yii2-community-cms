<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 23.03.16 11:51
 */

use tests\codeception\acceptance\AdminTester;

$I = new AdminTester($scenario);
$I->wantTo('add widget');

$scenario->skip('too complex');
$I->amGoingTo('add widget');
