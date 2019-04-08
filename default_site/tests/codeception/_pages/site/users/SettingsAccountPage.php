<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 16.03.16 10:47
 */

namespace tests\codeception\_pages\site\users;

use tests\codeception\_pages\LoginRequiredPage;
use app\helpers\ArrayHelper;
use app\helpers\RouteHelper;

/**
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class SettingsAccountPage extends LoginRequiredPage
{
    public $route = [RouteHelper::SITE_USERS_SETTINGS_ACCOUNT];

    /**
     * @param array $fields
     */
    public function changeAccount($fields = [])
    {
        $I = $this->actor;
        list($email, $newPassword, $password) = ArrayHelper::getValues($fields, ['email', 'newPassword', 'password']);

        if (!empty($email)) {
            $I->fillField('input[name="settings-form[email]"]', $email);
        }

        if (!empty($newPassword)) {
            $I->fillField('input[name="settings-form[new_password]"]', $newPassword);
        }

        $I->fillField('input[name="settings-form[current_password]"]', $password);

        $I->click('button[type="submit"]');
    }
}
