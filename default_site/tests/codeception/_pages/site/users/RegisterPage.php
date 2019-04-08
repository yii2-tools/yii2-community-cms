<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 15.03.16 9:57
 */

namespace tests\codeception\_pages\site\users;

use tests\codeception\_pages\Page;
use app\helpers\RouteHelper;

/**
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class RegisterPage extends Page
{
    public $route = [RouteHelper::SITE_USERS_REGISTRATION_REGISTER];

    /**
     * @param $username
     * @param $email
     * @param $password
     * @param $passwordConfirm
     */
    public function register($username, $email, $password, $passwordConfirm)
    {
        $I = $this->actor;

        $I->fillField('input[name="register-form[username]"]', $username);
        $I->fillField('input[name="register-form[email]"]', $email);
        $I->fillField('input[name="register-form[password]"]', $password);
        $I->fillField('input[name="register-form[password_repeat]"]', $passwordConfirm);
        $I->fillField('input[name="register-form[captcha]"]', 'testme');
        $I->click('button[type="submit"]');
    }
}