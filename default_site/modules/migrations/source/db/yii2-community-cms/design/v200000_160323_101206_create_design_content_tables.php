<?php

use yii\db\Migration;
use design\modules\content\models\ActivePlaceholder;

class v200000_160323_101206_create_design_content_tables extends Migration
{
    public function up()
    {
        $this->createTable(ActivePlaceholder::tableName(), [
            'name' => $this->string(255)->notNull(),
            'type' => 'TINYINT(1) unsigned NOT NULL',
            'content' => 'LONGBLOB NOT NULL',
            'status' => "TINYINT(1) unsigned NOT NULL DEFAULT '0'",
            'created_at' => $this->integer(11)->unsigned()->notNull(),
            'updated_at' => $this->integer(11)->unsigned()->notNull(),
            'PRIMARY KEY ([[name]])',
            'KEY ([[type]], [[status]])',
            'KEY ([[status]])',
            'KEY [[index_design_content_placeholders_updated_at]] ([[updated_at]])',
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB');

        $this->createTable(ActivePlaceholder::tableNameRelationChilds(), [
            'parent_name' => $this->string(255)->notNull(),
            'name' => $this->string(255)->notNull(),
            'PRIMARY KEY ([[parent_name]], [[name]])',
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB');

        $on = ActivePlaceholder::relationTableRoutesOn();
        $this->createTable(ActivePlaceholder::tableNameRelationRoutes(), [
            $on[0] => $this->string(255)->notNull(),
            $on[1] => $this->string(255)->notNull(),
            'PRIMARY KEY ([[' . $on[0] . ']], [[' . $on[1] . ']])',
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB');
    }

    public function down()
    {
        $this->dropTable(ActivePlaceholder::tableName());
        $this->dropTable(ActivePlaceholder::tableNameRelationChilds());
        $this->dropTable(ActivePlaceholder::tableNameRelationRoutes());
    }
}
