<?php

namespace tests\codeception\_pages\site\users;

use tests\codeception\_pages\Page;
use app\helpers\RouteHelper;

/**
 * Represents login page
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class LoginPage extends Page
{
    const LOGIN_LINK = 'Sign in';

    public $route = [RouteHelper::SITE_USERS_LOGIN];

    /**
     * @param string $username
     * @param string $password
     */
    public function login($username, $password)
    {
        $I = $this->actor;

        try {
            $I->see('Captcha');
            $I->fillField('input[name="login-form[captcha]"]', 'testme');
        } catch (\Exception $e) {
            $I->amGoingTo('login without captcha');
        }

        $I->fillField('input[name="login-form[login]"]', $username);
        $I->fillField('input[name="login-form[password]"]', $password);
        $I->click('button[type="submit"]');
    }
}
