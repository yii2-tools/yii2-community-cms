<?php

use yii\db\Migration;
use app\modules\integrations\models\Integration;

class v200000_160220_120430_create_engine_integrations_table extends Migration
{
    public function up()
    {
        $this->createTable(Integration::tableName(), [
            'vendor' => $this->string(255)->notNull(),
            'category' => $this->string(255),
            'context_id' => $this->string(255),
            'data' => 'LONGBLOB',
            'serializer' => $this->string(255)->notNull()->defaultValue('json'),
            'created_at' => $this->integer(11) . ' unsigned NOT NULL',
            'updated_at' => $this->integer(11) . ' unsigned NOT NULL',
            'PRIMARY KEY ([[vendor]], [[category]], [[context_id]])',
            'KEY [[index_engine_integrations_vendor_category]] ([[vendor]], [[category]])',
            'KEY [[index_engine_integrations_vendor_context_id]] ([[vendor]], [[context_id]])',
            'KEY [[index_engine_integrations_vendor]] ([[vendor]])',
            'KEY [[index_engine_integrations_updated_at]] ([[updated_at]])',
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB');
    }

    public function down()
    {
        $this->dropTable(Integration::tableName());
    }
}
