<?php

use yii\tools\params\models\ActiveParam;
use app\helpers\ModuleHelper;
use app\modules\migrations\components\ConvertMigrationAbstract;
use site\modules\forum\models\Section;
use admin\modules\users\components\Item;

class v200000_160430_073601_convert_or_create_forum_sections_table extends ConvertMigrationAbstract
{
    /** @inheritdoc */
    public $upTableRequired = false;

    protected function tableName()
    {
        return Section::tableName();
    }

    protected function convertUp($data)
    {
        $table = $this->tableName();

        $this->createTable($table, [
            'id' => $this->integer(11)->unsigned()->notNull() . ' AUTO_INCREMENT',
            'title' => $this->string(255)->notNull(),
            'slug' => $this->string(255)->notNull(),
            'rbac_on' => "TINYINT unsigned NOT NULL DEFAULT '0'",
            'rbac_item' => $this->string(255),
            'position' => "TINYINT unsigned NOT NULL DEFAULT '0'",
            'created_at' => $this->integer(11)->unsigned()->notNull(),
            'updated_at' => $this->integer(11)->unsigned()->notNull(),
            'PRIMARY KEY ([[id]])',
            'UNIQUE KEY [[index_unique_forum_sections_slug]] ([[slug]])',
            'KEY [[index_forum_sections_position]] ([[position]])',
            'KEY [[index_forum_sections_updated_at]] ([[updated_at]])',
        ], $this->tableOptions());

        if (!empty($data)) {
            $authManager = Yii::$app->getAuthManager();
            foreach ($data as $row) {
                try {
                    $this->insert($authManager->itemTable, [
                        'name' => $itemName = 'ACCESS_FORUM_SECTIONS_' . $row['forum_section_id'],
                        'type' => Item::TYPE_PERMISSION,
                        'description' => Yii::t(
                            ModuleHelper::ADMIN_FORUM,
                            'Access to forum section "{secureItemDescriptionParam}"',
                            ['secureItemDescriptionParam' => $row['section_title']]
                        ),
                        'active' => 1,
                        'created_at' => time(),
                        'updated_at' => time(),
                    ]);

                    $this->insert($table, [
                        'id' => $row['forum_section_id'],
                        'title' => $row['section_title'],
                        'slug' => $row['section_href'],
                        'rbac_on' => 1,
                        'rbac_item' => $itemName,
                        'position' => $row['pos'],
                        'created_at' => time(),
                        'updated_at' => time(),
                    ]);

                    ActiveParam::updateAllCounters(
                        ['value' => 1],
                        ['or', ['name' => 'forum_sections_count'], ['name' => 'permissions_count']]
                    );
                } catch (\Exception $e) {
                    Yii::error($e, __METHOD__);
                }
            }
        }
    }

    protected function convertDown($data)
    {
        // BROKEN rbac tables: unused permissions and relations.
        // Convert down only in full migration circle (up->down or down->up).

        $this->createTable($this->oldTableName(), [
            'forum_section_id' => $this->integer(11)->unsigned()->notNull() . " AUTO_INCREMENT PRIMARY KEY",
            'section_title' => $this->string(50)->notNull(),
            'section_href' => $this->string(50)->notNull(),
            'pos' => $this->integer(11)->unsigned()->notNull()->defaultValue('0'),
            'UNIQUE KEY [[section_href]] ([[section_href]])',
        ], $this->tableOptions());

        if (!empty($data)) {
            foreach ($data as $row) {
                try {
                    $this->insert($this->oldTableName(), [
                        'forum_section_id' => $row['id'],
                        'section_title' => $row['title'],
                        'section_href' => $row['slug'],
                        'pos' => $row['position'],
                    ]);
                } catch (\Exception $e) {
                    Yii::error($e, __METHOD__);
                }

                ActiveParam::updateAllCounters(
                    ['value' => -1],
                    ['or', ['name' => 'forum_sections_count']/*, ['name' => 'permissions_count']*/]
                );
            }
        }
    }
}
