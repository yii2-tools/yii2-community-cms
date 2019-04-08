<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 17.03.16 16:06
 */

namespace tests\codeception\acceptance;

/**
 * Represents user with id 1 and high privileges
 */
class AdminTester extends UserTester
{
    public $credentials = [
        'username' => 'admin',
        'password' => '123456',
    ];

    public $email = 'admin@domain.ltd';
}