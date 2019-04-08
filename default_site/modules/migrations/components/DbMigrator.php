<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 30.01.16 7:53
 */

namespace app\modules\migrations\components;

use Exception;
use UnexpectedValueException;
use Yii;
use yii\db\Exception as DbException;
use yii\base\Component;
use app\helpers\ModuleHelper;
use app\modules\migrations\MigratorInterface;
use app\modules\migrations\controllers\MigrateController;
use app\modules\migrations\models\EngineDbMigration;

/**
 * @todo ОТРЕФАКТОРИТЬ
 *
 * Class DbMigrator
 * @package app\modules\migrations\components
 */
class DbMigrator extends Component implements MigratorInterface
{
    const DB_VERSION_FULL_PATTERN = '/^v?((\d{6})_(\d{6})_(\d{6}))(_.*?)?$/';
    const DB_VERSION_WITH_DATE_PATTERN = '/^v?(\d{6}_\d{6})(_.*?)?$/';
    const DB_VERSION_ONLY_PATTERN = '/^v?(\d{6})(_.*?)?$/';

    /** @var string */
    protected $dbVersion = '';

    /** @var string */
    protected $lastAppliedMigration = '';

    /** @var MigrateController */
    protected $controller = null;

    /** @var string */
    protected $lock;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->lock = Yii::getAlias(Yii::$app->getModule(ModuleHelper::MIGRATIONS)->params['lock_file']);
    }

    /**
     * Checks actuality of current version
     * @return bool
     */
    public function checkVersion()
    {
        Yii::trace('Comparing current engine version with database version...', __METHOD__);
        $lastAppliedMigration = $this->getLastAppliedMigration();
        preg_match(self::DB_VERSION_ONLY_PATTERN, $lastAppliedMigration, $matches);
        $isActual = ($currentDbVersion = $this->getDbVersion()) === intval($matches[1]);
        Yii::info('Last applied migration: ' . $lastAppliedMigration . PHP_EOL .
                  'Database version for current engine: ' . $currentDbVersion . PHP_EOL .
                  ($isActual ? 'Database is up-to-date' : 'New version of database available'), __METHOD__);

        return $isActual;
    }

    /**
     * Updating version of target source (to actual)
     */
    public function update()
    {
        $this->lock();
        try {
            ob_start();
            $this->getMigrateController()->runAction('to', [
                $this->getDbVersion(),
                'enableBuffer' => true,
                'disableHistoryTableCheck' => true,
            ]);
            ob_end_clean();
            $this->unlock();
        } catch (Exception $e) {
            // @todo: this method needs refactoring for php versions 5.5+ (finally statement implemented)
            ob_end_clean();
            $this->unlock();

            throw $e;
        }
    }

    /**
     * Reverting to previous version of target source
     * @return void
     */
    public function revert()
    {
        // TODO: Implement revert() method.
    }

    /**
     * @return int
     * @throws \UnexpectedValueException
     */
    public function getDbVersion()
    {
        if (!Yii::$app->version) {
            throw new UnexpectedValueException('Yii::$app->version not defined');
        }
        if (!$this->dbVersion) {
            $this->dbVersion = intval(str_pad(preg_replace('/[^\d]/', '', Yii::$app->version), 6, 0));
        }

        return $this->dbVersion;
    }

    protected function getLastAppliedMigration()
    {
        Yii::trace('Obtaining last applied migration from \'' . EngineDbMigration::tableName() . '\'', __METHOD__);
        if (!$this->lastAppliedMigration) {
            try {
                $this->lastAppliedMigration = EngineDbMigration::find()->max('[[version]]');
            } catch (DbException $e) {
                if (intval(substr(preg_replace('/[^\d]/', '', $e->getCode()), 0, 2)) === 42) {
                    Yii::trace('Table \'' . EngineDbMigration::tableName() . '\' doesn\'t exists', __METHOD__);
                    $this->getMigrateController()->runAction('history-create', ['enableBuffer' => true]);
                    $this->lastAppliedMigration = EngineDbMigration::find()->max('[[version]]');
                } else {
                    throw $e;
                }
            }
        }

        return $this->lastAppliedMigration;
    }

    protected function getMigrateController()
    {
        if (!$this->controller) {
            $consoleDefaultConfig = require(YII_APP_PATH_CONFIG
                . implode(DIRECTORY_SEPARATOR, ['', 'default', 'console.php']));
            foreach ($consoleDefaultConfig['aliases'] as $alias => $path) {
                if (false === Yii::getAlias($alias, false)) {
                    Yii::setAlias($alias, $path);
                }
            }
            $this->controller = Yii::$app->getModule(ModuleHelper::MIGRATIONS)->createController('migrate')[0];
        }

        return $this->controller;
    }

    protected function isLocked()
    {
        return file_exists($this->lock);
    }

    protected function lock()
    {
        if ($this->isLocked()) {
            die('This site is temporarily unavailable...');
        }

        file_put_contents($this->lock, '');
    }

    protected function unlock()
    {
        unlink($this->lock);
    }
}
