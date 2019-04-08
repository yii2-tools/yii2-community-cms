<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 30.01.16 7:54
 */

namespace app\modules\migrations;

/**
 * Interface Migrator
 * @package app\modules\migrations\interfaces
 */
interface MigratorInterface
{
    /**
     * Checks actuality of current version
     * @return bool
     */
    public function checkVersion();

    /**
     * Updating version of target source (to actual)
     * @return void
     */
    public function update();

    /**
     * Reverting to previous version of target source
     * @return void
     */
    public function revert();
}
