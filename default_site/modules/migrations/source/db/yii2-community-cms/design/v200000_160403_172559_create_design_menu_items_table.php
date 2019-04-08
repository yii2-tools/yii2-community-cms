<?php

use yii\db\Migration;
use design\modules\menu\models\MenuItem;

class v200000_160403_172559_create_design_menu_items_table extends Migration
{
    public function up()
    {
        $this->createTable(MenuItem::tableName(), [
            'id' => $this->integer(11)->unsigned()->notNull() . ' AUTO_INCREMENT',
            'label' => $this->string(255)->notNull(),
            'url_to' => $this->string(255)->notNull(),
            'is_route' => "TINYINT unsigned NOT NULL DEFAULT '0'",
            'position' => "TINYINT NOT NULL DEFAULT '0'",
            'created_at' => $this->integer(11)->unsigned()->notNull(),
            'updated_at' => $this->integer(11)->unsigned()->notNull(),
            'PRIMARY KEY ([[id]])',
            'KEY [[index_design_menu_items_updated_at]] ([[updated_at]])',
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB');
    }

    public function down()
    {
        $this->dropTable(MenuItem::tableName());
    }
}
