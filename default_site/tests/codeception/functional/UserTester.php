<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 17.03.16 16:06
 */

namespace tests\codeception\functional;

/**
 * Represents user tester
 */
class UserTester extends \FunctionalTester
{
    public $credentials = [
        'username' => 'user',
        'password' => '123456',
    ];

    public $email = 'user@domain.ltd';
}