<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 18.03.16 13:08
 */

namespace tests\codeception\_pages\site\users;

use tests\codeception\_pages\Page;
use app\helpers\RouteHelper;

/**
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class RegisterConfirmResendPage extends Page
{
    public $route = [RouteHelper::SITE_USERS_REGISTRATION_RESEND];

    public function resend($email)
    {
        $I = $this->actor;

        $I->fillField('input[name="resend-form[email]"]', $email);
        $I->fillField('input[name="resend-form[captcha]"]', 'testme');
        $I->click('button[type="submit"]');
    }
}
