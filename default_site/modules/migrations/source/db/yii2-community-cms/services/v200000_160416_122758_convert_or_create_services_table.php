<?php

use app\modules\migrations\components\ConvertMigrationAbstract;
use app\modules\services\models\Service;

class v200000_160416_122758_convert_or_create_services_table extends ConvertMigrationAbstract
{
    /** @inheritdoc */
    public $upTableRequired = false;

    /** @inheritdoc */
    public $upTableSkipExists = true;

    protected function tableName()
    {
        return Service::tableName();
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
            'service_id' => $this->integer(11)->unsigned()->notNull() . ' AUTO_INCREMENT',
            'service_key' => $this->string(32)->notNull(),
            'service_name' => $this->string(100)->notNull(),
            'service_params' => $this->string(200)->notNull(),
            'PRIMARY KEY ([[service_id]])',
            'UNIQUE KEY [[index_unique_service_key]] ([[service_key]])',
            'UNIQUE KEY [[index_unique_service_name]] ([[service_name]])',
        ], $this->tableOptions());

        if (!empty($data)) {
            foreach ($data as $row) {
                $this->insert($table, $row);
            }
        }
    }
}
