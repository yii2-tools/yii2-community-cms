<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 28.01.16 4:36
 */

namespace app\modules\selftest\models;

interface TestInterface
{
    /**
     * @return string
     */
    public function getTitle();

    /**
     * @return TestResult
     */
    public function execute();

    /**
     * Fixing and restoring correct application state if test failed
     * @return void
     */
    public function fallback();
}
