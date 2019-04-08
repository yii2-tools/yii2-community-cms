<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 16.03.16 11:02
 */

namespace tests\codeception\_pages;

/**
 * Represents abstract page with access control
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
abstract class AccessRequiredPage extends Page
{
    const ACCESS_DENIED = 403;

    protected static $accessError;

    public static function accessError()
    {
        return static::$accessError;
    }

    /**
     * @inheritdoc
     * @param \Codeception\Actor|\AcceptanceTester|\FunctionalTester $I
     * @param array $params
     * @return null|\yii\codeception\BasePage
     */
    public static function openBy($I, $params = [], $options = [])
    {
        $page = parent::openBy($I, $params, $options);
        static::ensureOpenedBy($I, $page, $options);
        return $page;
    }

    protected static function ensureOpenedBy($I, $page, $options = [])
    {
        static::$accessError = null;
        try {
            $I->dontSee('403');
        } catch (\Exception $e) {
            static::$accessError = static::ACCESS_DENIED;
            throw $e;
        }
        parent::ensureOpenedBy($I, $page, $options);
    }
}