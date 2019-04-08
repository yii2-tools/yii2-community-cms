<?php

use app\modules\migrations\components\ConvertMigrationAbstract;
use site\modules\plugins\models\PluginData;

class v200000_160418_152758_convert_or_create_plugins_data_table extends ConvertMigrationAbstract
{
    /** @inheritdoc */
    public $upTableRequired = false;

    /** @inheritdoc */
    public $upTableSkipExists = true;

    protected function tableName()
    {
        return PluginData::tableName();
    }

    protected function convertUp($data)
    {
        $this->convertInternal($data);
    }

    protected function convertDown($data)
    {
        $this->convertInternal($data);
    }

    protected function convertInternal($data)
    {
        $table = $this->tableName();

        $this->createTable($table, [
                'plugin_data_id' => $this->integer(11)->unsigned()->notNull() . ' AUTO_INCREMENT',
                'plugin_key' => $this->string(32)->notNull(),
                'pd_key1' => $this->string(400),
                'pd_key2' => $this->string(400),
                'pd_key3' => $this->string(400),
                'pd_key4' => $this->string(400),
                'pd_key5' => $this->string(400),
                'pd_key6' => $this->string(400),
                'pd_key7' => $this->string(400),
                'pd_key8' => $this->string(400),
                'pd_key9' => $this->string(400),
                'PRIMARY KEY ([[plugin_data_id]])',
                'KEY [[index_plugin_data_key]] ([[plugin_key]])',
            ], $this->tableOptions());

        if (!empty($data)) {
            foreach ($data as $row) {
                $this->insert($table, $row);
            }
        }
    }
}
