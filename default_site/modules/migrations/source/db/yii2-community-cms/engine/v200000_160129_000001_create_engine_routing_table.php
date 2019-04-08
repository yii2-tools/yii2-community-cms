<?php

use yii\db\Schema;
use yii\db\Migration;
use app\modules\routing\models\Route;

class v200000_160129_000001_create_engine_routing_table extends Migration
{
    public function up()
    {
        $this->createTable(Route::tableName(), [
            'id' => $this->integer(11) . ' UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY',
            'module' => $this->string(255)->notNull(),
            'default_url_pattern' => $this->string(255)->notNull(),
            'url_pattern' => $this->string(255)->notNull(),
            'route_pattern' => $this->string(255)->notNull(),
            'route' => $this->string(255)->notNull(),
            'params' => $this->string(255)->notNull()->defaultValue('{}'),
            'description' => $this->string(255) . ' DEFAULT NULL',
            'created_at' => $this->integer(11) . ' unsigned NOT NULL',
            'updated_at' => $this->integer(11) . ' unsigned NOT NULL',
            'UNIQUE KEY `index_unique_url_pattern` (`url_pattern`)',
            'KEY `index_updated_at` (`updated_at`)'
        ], $this->tableOptions());
    }

    public function down()
    {
        $this->dropTable(Route::tableName());
    }

    private function tableOptions()
    {
        switch ($this->db->driverName) {
            case 'mysql':
                return 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
            default:
                return null;
        }
    }
}
