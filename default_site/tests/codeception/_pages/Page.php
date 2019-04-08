<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 16.03.16 9:51
 */

namespace tests\codeception\_pages;

use yii\codeception\BasePage;

/**
 * Represents abstract page
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
abstract class Page extends BasePage
{
    const NOT_FOUND_ERROR = 404;

    protected static $notFoundError;

    public static function notFoundError()
    {
        return static::$notFoundError;
    }

    /**
     * @inheritdoc
     * @param array $options
     * @return static
     */
    public static function openBy($I, $params = [], $options = [])
    {
        $page = parent::openBy($I, $params);
        static::ensureFound($I);
        return $page;
    }

    /**
     * Creates a page instance if test guy already on this page.
     * Otherwise it is not correct and something doing wrong, error be occurred
     * @param \Codeception\Actor|\AcceptanceTester|\FunctionalTester $I the test guy instance
     * @return static the page instance
     */
    public static function openedBy($I)
    {
        $page = new static($I);
        static::ensureOpenedBy($I, $page);
        return $page;
    }

    /**
     * @param \Codeception\Actor|\AcceptanceTester|\FunctionalTester $I
     * @param Page $page
     * @param array $options
     */
    protected static function ensureOpenedBy($I, $page, $options = [])
    {
        try {
            static::ensureFound($I);
            static::ensureOpenedByCondition($I, $page);
        } catch (\Exception $e) {
            $I->comment('I was convinced that I not on this page right now');
            throw $e;
        }
    }

    final protected static function ensureFound($I)
    {
        static::$notFoundError = null;
        try {
            $I->dontSee('404');
        } catch (\Exception $e) {
            static::$notFoundError = static::NOT_FOUND_ERROR;
            throw $e;
        }
    }

    /**
     * @param \Codeception\Actor|\AcceptanceTester|\FunctionalTester $I
     * @param Page $page
     * @param array $options
     */
    protected static function ensureOpenedByCondition($I, $page, $options = [])
    {
        $I->seeCurrentUrlEquals($page->getUrl());
    }
}