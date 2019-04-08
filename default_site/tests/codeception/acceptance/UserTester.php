<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 17.03.16 16:06
 */

namespace tests\codeception\acceptance;

/**
 * Represents user tester
 */
class UserTester extends \AcceptanceTester
{
    public $credentials = [
        'username' => 'user',
        'password' => '123456',
    ];

    public $email = 'user@domain.ltd';
}