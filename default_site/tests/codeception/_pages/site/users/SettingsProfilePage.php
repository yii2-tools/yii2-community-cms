<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 16.03.16 12:55
 */

namespace tests\codeception\_pages\site\users;

use tests\codeception\_pages\LoginRequiredPage;
use app\helpers\ArrayHelper;
use app\helpers\RouteHelper;

/**
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class SettingsProfilePage extends LoginRequiredPage
{
    public $route = [RouteHelper::SITE_USERS_SETTINGS_PROFILE];

    public function changeProfile($fields = [])
    {
        $I = $this->actor;
        list($name, $location, $image) = ArrayHelper::getValues($fields, ['name', 'location', 'image']);

        $I->fillField('input[name="Profile[name]"]', $name);
        $I->fillField('input[name="Profile[location]"]', $location);
        if (!empty($image)) {
            $I->attachFile('input[type="file"]', $image);
        }
        $I->click('button[type="submit"]');
    }
}