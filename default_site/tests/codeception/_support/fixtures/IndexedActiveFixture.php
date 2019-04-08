<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 20.03.16 13:45
 */

namespace tests\codeception\_support\fixtures;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\test\ActiveFixture as BaseActiveFixture;

/**
 * Active fixture with $dataFile as indexed arrays (without keys as field names)
 * Instead of explicit keys specification, this fixture use additional file
 * named $dataColumnsFile which contains array of field names for batch insert operation.
 * @package tests\codeception\fixtures
 */
class IndexedActiveFixture extends BaseActiveFixture
{
    /** @var string */
    public $dataColumnsFile;

    /**
     * @inheritdoc
     */
    protected function getData()
    {
        $data = parent::getData();
        if (ArrayHelper::isIndexed($data)) {
            $data = $this->makeAssociative($data);
        }

        return $data;
    }

    /**
     * Makes data array associative
     * @param array $data
     * @return array
     */
    protected function makeAssociative($data)
    {
        foreach ($data as &$record) {
            $record = $this->assignColumnNames($record);
        }

        return $data;
    }

    /**
     * @param array $record
     * @return array
     */
    protected function assignColumnNames($record)
    {
        $associativeRecord = [];
        $index = 0;
        $columnNames = $this->getDataColumns();
        foreach ($columnNames as $columnName) {
            $associativeRecord[$columnName] = $record[$index];
            ++$index;
        }

        return $associativeRecord;
    }

    /**
     * Returns array of columns for $dataFile
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    protected function getDataColumns()
    {
        if ($this->dataColumnsFile === null) {
            $class = new \ReflectionClass($this);
            $dataColumnsFile = dirname($class->getFileName()) . '/data/'
                . $this->getTableSchema()->fullName . '_columns.php';

            return is_file($dataColumnsFile) ? require($dataColumnsFile) : [];
        } elseif ($this->dataColumnsFile === false) {
            return [];
        }
        $dataColumnsFile = Yii::getAlias($this->dataColumnsFile);
        if (is_file($dataColumnsFile)) {
            return require($dataColumnsFile);
        }

        throw new InvalidConfigException("Fixture data columns file does not exist: {$this->dataColumnsFile}");
    }
}