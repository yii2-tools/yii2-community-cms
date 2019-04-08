<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 08.04.16 9:02
 */

namespace tests\codeception\_support\traits;

use Yii;

trait MigrationFixtureTrait
{
    /*
     * Example:
     * ['files/yii2-community-cms/design/v200000_160402_080220_create_custom_design_pack']
     *
     * @var array
     */
    //public $migrations;

    public function getMigrations()
    {
        $migrations = [];

        foreach ($this->migrations as $migration) {
            require_once(Yii::getAlias('@migrations/source/' . $migration . '.php'));
            $class = '\\' . preg_replace('/.*\//', '', $migration);
            $migrations[] = new $class();
        }

        return $migrations;
    }

    public function reloadMigration()
    {
        $migrations = $this->getMigrations();

        ob_start();
        foreach ($migrations as $migration) {
            $migration->down();
            $migration->up();
        }
        ob_end_clean();
    }
}