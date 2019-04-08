<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 18.03.16 15:33
 */

namespace tests\codeception\_pages\site\users;

use tests\codeception\_pages\Page;
use tests\codeception\_pages\traits\ParameterizedPage;
use app\helpers\RouteHelper;

/**
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class RecoveryResetPage extends Page
{
    use ParameterizedPage;

    public static $params = ['key'];

    public $route = [RouteHelper::SITE_USERS_RECOVERY_RESET];

    public function reset($newPassword, $confirmNewPassword)
    {
        $I = $this->actor;

        $I->fillField('input[name="recovery-form[password]"]', $newPassword);
        $I->fillField('input[name="recovery-form[password_repeat]"]', $confirmNewPassword);
        $I->click('button[type="submit"]');
    }
}