<?php

use yii\db\Migration;
use site\modules\design\helpers\ModuleHelper;
use design\modules\packs\models\DesignPack;

class v200000_160328_191459_create_design_packs_table extends Migration
{
    public function up()
    {
        $this->createTable(DesignPack::tableName(), [
            'name' => $this->string(10)->notNull(),
            'title' => $this->string(255)->notNull(),
            'description' => $this->string(255),
            'preview' => $this->string(255),
            'version' => $this->string(15)->notNull(),
            'uploaded_at' => $this->integer(11)->unsigned()->notNull(),
            'updated_at' => $this->integer(11)->unsigned()->notNull(),
            'PRIMARY KEY ([[name]])',
            'KEY [[index_design_packs_updated_at]] ([[updated_at]])',
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB');

        $this->batchInsert(
            DesignPack::tableName(),
            [
                'name', 'title', 'description', 'preview', 'version', 'uploaded_at', 'updated_at'
            ],
            [
                [
                    'custom',
                    Yii::t(ModuleHelper::DESIGN_PACKS, 'Design pack for my site'),
                    Yii::t(ModuleHelper::DESIGN_PACKS, 'This is pre-installed design pack.'
                        . ' You can freely modify its contents or download and activate another one.'
                        . ' Activated design pack cannot be removed until you install a replacement'),
                    'images/preview.png',
                    '2.0.0',
                    time(),
                    time()
                ]
            ]
        );
    }

    public function down()
    {
        $this->dropTable(DesignPack::tableName());
    }
}
