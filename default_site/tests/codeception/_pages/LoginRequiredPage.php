<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 16.03.16 11:12
 */

namespace tests\codeception\_pages;

use tests\codeception\_pages\site\users\LoginPage;
use tests\codeception\_pages\site\users\SettingsProfilePage;

/**
 * Represents abstract page with access control in the form of login checks
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
abstract class LoginRequiredPage extends AccessRequiredPage
{
    /**
     * @inheritdoc
     */
    public static function openBy($I, $params = [], $options = [])
    {
        try {
            return parent::openBy($I, $params, $options);
        } catch (\Exception $e) {
            return static::tryToLogin($I, $options);
        }
    }

    /**
     * @inheritdoc
     */
    protected static function ensureOpenedBy($I, $page)
    {
        $I->expectTo("be redirected from this page to login page if I not authenticated");
        parent::ensureOpenedBy($I, $page);
    }

    /**
     * @param \Codeception\Actor|\AcceptanceTester|\FunctionalTester $I
     * @param array $options
     * @return static
     * @throws \Exception
     */
    protected static function tryToLogin($I, $options = [])
    {
        $loginPage = LoginPage::openedBy($I);
        $credentials = static::provideCredentials($I, $options);

        if (empty($credentials)) {
            $I->fail("I don't have any credentials for log in");
        }

        $I->amGoingTo('log in and be redirected back to requested page');

        $loginPage->login($credentials['username'], $credentials['password']);

        if (static::className() === SettingsProfilePage::className()) {
            return static::openedBy($I);
        }

        try {
            return static::openedBy($I);
        } catch (\Exception $e) {
            if (isset(static::$accessError)) {
                throw $e;
            }
            SettingsProfilePage::openedBy($I);
            $I->fail('Ooops... Looks like I forgot to fill all profile fields and be redirected'
                . ' to profile settings page instead');
        }
    }

    /**
     * @param \Codeception\Actor|\AcceptanceTester|\FunctionalTester $I
     * @param array $options
     * @return null
     */
    protected static function provideCredentials($I, $options = [])
    {
        if (isset($I->credentials)) {
            return $I->credentials;
        } elseif (isset($options['credentials'])) {
            return $options['credentials'];
        }

        return null;
    }
}