<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 18.03.16 15:21
 */

namespace tests\codeception\_pages\site\users;

use tests\codeception\_pages\Page;
use app\helpers\RouteHelper;

/**
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class RecoveryRequestPage extends Page
{
    public $route = [RouteHelper::SITE_USERS_RECOVERY_REQUEST];

    public function recovery($email)
    {
        $I = $this->actor;

        $I->fillField('input[name="recovery-form[email]"]', $email);
        $I->fillField('input[name="recovery-form[captcha]"]', 'testme');

        $I->click('button[type="submit"]');
    }
}