<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 07.02.16 16:40
 */

namespace app\modules\migrations\components;

use Exception;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\db\Migration as BaseMigration;
use app\helpers\ModuleHelper;

abstract class ConvertMigrationAbstract extends BaseMigration
{
    /**
     * Skip UP migration logic if source table for converting doesn't exists.
     * Default to skip.
     * @var bool
     */
    public $upTableRequired = true;

    /**
     * Skip UP migration logic if target table already exists in database.
     * Default to false, means that this table will be deleted and created again.
     * @var bool
     */
    public $upTableSkipExists = false;

    /**
     * Enable/Disable caching convert table data for temporarily backup
     * @var bool
     */
    public $migrationsCacheEnabled = true;

    /**
     * @var bool
     */
    public $foreignChecks = false;

    /**
     * Data, saved in memory, between convert stages
     * @var array
     */
    protected $tmpData = [];

    /**
     * Override if new table name should be renamed
     * @return string
     */
    protected function oldTableName()
    {
        return $this->tableName();
    }

    /**
     * @inheritdoc
     */
    final public function up()
    {
        return parent::up();
    }

    /**
     * @inheritdoc
     */
    final public function down()
    {
        if (!$this->foreignChecks) {
            $this->execute('SET foreign_key_checks = 0;');
        }

        $result = false;
        try {
            $result = parent::down();
        } catch (\Exception $e) {
            if (!$this->foreignChecks) {
                $this->execute('SET foreign_key_checks = 1;');
            }

            throw $e;
        }

        if (!$this->foreignChecks) {
            $this->execute('SET foreign_key_checks = 1;');
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        Yii::trace('Converting old database schemas started', __METHOD__);

        $db = $this->getDb();
        $table = $this->oldTableName();
        $schema = $db->getTableSchema($table, true);

        if ($this->upTableRequired && is_null($schema)) {
            Yii::info('Table ' . $table . ' doesn\'t exists, converting skipped', __METHOD__);

            return;
        }

        if ($this->upTableSkipExists && $db->getTableSchema($newTable = $this->tableName(), true)) {
            Yii::info('Table ' . $newTable . ' already exists, converting skipped', __METHOD__);

            return;
        }

        try {
            if (!is_null($schema)) {
                $this->setTmpData($table . '_up', $this->convertUpData());
                $this->dropTable($table);
            }
            $this->convertUp($this->getTmpData($table . '_up'));
        } catch (Exception $e) {
            Yii::error("Converting $table FAILED" . PHP_EOL . VarDumper::dumpAsString($e->__toString()), __METHOD__);
            $this->safeDown();

            throw $e;
        }
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        Yii::trace('Reverting old database schemas started', __METHOD__);

        $table = $this->tableName();

        if (!($schema = $this->getDb()->getTableSchema($table, true))) {
            Yii::info('Table ' . $table . ' doesn\'t exists, reverting skipped', __METHOD__);

            return;
        }

        try {
            $this->setTmpData($table . '_down', $this->convertDownData());
            $this->dropTable($table);
            $this->convertDown($this->getTmpData($table . '_down'));
        } catch (Exception $e) {
            Yii::error('Reverting old database schemas FAILED'
                . PHP_EOL . VarDumper::dumpAsString($e->__toString()), __METHOD__);

            throw $e;
        }
    }

    protected function setTmpData($key, $value)
    {
        $this->tmpData[$key] = $value;

        if ($this->migrationsCacheEnabled) {
            Yii::$app->getModule(ModuleHelper::MIGRATIONS)->cache->set($key, $value);
        }
    }

    protected function getTmpData($key)
    {
        if (($value = ArrayHelper::getValue($this->tmpData, $key)) !== null || !$this->migrationsCacheEnabled) {
            return $value;
        }

        return Yii::$app->getModule(ModuleHelper::MIGRATIONS)->cache->get($key);
    }

    protected function convertUpData()
    {
        return $this->getDb()->createCommand('SELECT * FROM ' . $this->oldTableName())->queryAll();
    }

    protected function convertDownData()
    {
        return $this->getDb()->createCommand('SELECT * FROM ' . $this->tableName())->queryAll();
    }

    protected function tableOptions()
    {
        switch ($this->db->driverName) {
            case 'mysql':
                return 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
            default:
                return null;
        }
    }

    /**
     * @return string
     */
    abstract protected function tableName();

    /**
     * @param $data
     * @return void
     * @throws \Exception
     */
    abstract protected function convertUp($data);

    /**
     * Restriction: down migration isn't allowed if source table doesn't exists!
     * @param $data
     * @return void
     * @throws \Exception
     */
    abstract protected function convertDown($data);
}
