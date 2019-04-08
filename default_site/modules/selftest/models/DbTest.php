<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 30.01.16 7:45
 */

namespace app\modules\selftest\models;

use Yii;
use app\helpers\ModuleHelper;

class DbTest extends Test
{
    public function getTitle()
    {
        return 'Database';
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        Yii::trace('Checking database connection...', __METHOD__);

        try {
            Yii::$app->db->open();
        } catch (\Exception $e) {
            // trying to load db params from old config format (if present).
            $filenameOld = Yii::getAlias('@app/system/mysql.db');

            if (!file_exists($filenameOld)) {
                throw $e;
            }

            Yii::info("Old format db config file '$filenameOld' found, migrating config data...", __METHOD__);
            if (Yii::$app->db->isActive) {
                Yii::$app->db->close();
            }

            $dbOldLines = file($filenameOld);
            $dbOldParams = [];
            foreach ($dbOldLines as $line) {
                $parts = explode('=', $line);
                $dbOldParams[trim($parts[0])] = trim($parts[1]);
            }

            $filename = Yii::getAlias('@app/config/prod/yii2_community_cms_site/db.php');
            $contents = file_get_contents($filename);
            $contents = str_replace('{yii2_community_cms_site_db_host}', $dbOldParams['dbhost'], $contents);
            $contents = str_replace('{yii2_community_cms_site_db_name}', $dbOldParams['dbname'], $contents);
            $contents = str_replace('{yii2_community_cms_site_db_user}', $dbOldParams['dbuser'], $contents);
            $contents = str_replace('{yii2_community_cms_site_db_pass}', $dbOldParams['dbpass'], $contents);
            if (false !== file_put_contents($filename, $contents)) {
                unlink($filenameOld);
            }

            Yii::$app->db->dsn = 'mysql:host=' . $dbOldParams['dbhost'] . ';dbname=' . $dbOldParams['dbname'];
            Yii::$app->db->username = $dbOldParams['dbuser'];
            Yii::$app->db->password = $dbOldParams['dbpass'];
            Yii::$app->db->open();
        }

        Yii::trace('Checking database version...', __METHOD__);

        $dbMigrator = Yii::$app->getModule(ModuleHelper::MIGRATIONS)->dbMigrator;

        if (!$dbMigrator->checkVersion()) {
            // migration logging required.
            if (YII_ENV_PROD) {
                Yii::$app->log->targets['migrations']->enabled = true;
            }
            $dbMigrator->update();
        }
    }

    /**
     * Fixing and restoring correct application state if test failed
     */
    public function fallback()
    {
        ;
    }
}
