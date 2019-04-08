<?php

use yii\db\Schema;
use yii\db\Migration;
use app\models\ListValue;

class v200000_160210_161034_create_engine_list_values_table extends Migration
{
    public function up()
    {
        $this->createTable(ListValue::tableName(), [
            'id' => $this->integer(11) . ' unsigned NOT NULL AUTO_INCREMENT',
            'list_id' => $this->string(255)->notNull()->defaultValue(''),
            'value' => $this->string(255),
            'created_at' => $this->integer(11) . ' unsigned NOT NULL',
            'updated_at' => $this->integer(11) . ' unsigned NOT NULL',
            'PRIMARY KEY ([[id]])',
            'KEY ([[id]])',
            'KEY [[index_engine_list_value_list_id]] ([[list_id]])',
            'KEY [[index_engine_list_value_updated_at]] ([[updated_at]])',
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB');
    }

    public function down()
    {
        $this->dropTable(ListValue::tableName());
    }
}
