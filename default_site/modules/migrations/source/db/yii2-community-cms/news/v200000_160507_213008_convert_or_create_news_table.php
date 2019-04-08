<?php

use yii\tools\params\models\ActiveParam;
use app\helpers\ModuleHelper;
use app\modules\migrations\components\ConvertMigrationAbstract;
use site\modules\news\models\NewsRecord;
use admin\modules\users\components\Item;

class v200000_160507_213008_convert_or_create_news_table extends ConvertMigrationAbstract
{
    /** @inheritdoc */
    public $upTableRequired = false;

    protected function tableName()
    {
        return NewsRecord::tableName();
    }

    protected function convertUp($data)
    {
        $table = $this->tableName();

        $this->createTable($table, [
            'id' => $this->integer(11)->unsigned()->notNull() . ' AUTO_INCREMENT',
            'title' => $this->string(255)->notNull(),
            'slug' => $this->string(255)->notNull(),
            'content' => 'LONGTEXT NOT NULL',
            'rbac_on' => "TINYINT unsigned NOT NULL DEFAULT '0'",
            'rbac_item' => $this->string(255),
            'created_by' => $this->integer(11)->unsigned()->notNull(),
            'updated_by' => $this->integer(11)->unsigned()->defaultValue(0),
            'created_at' => $this->integer(11)->unsigned()->notNull(),
            'updated_at' => $this->integer(11)->unsigned()->notNull(),
            'PRIMARY KEY ([[id]])',
            'UNIQUE KEY [[index_unique_pages_slug]] ([[slug]])',
            'UNIQUE KEY [[index_unique_pages_rbac_item]] ([[rbac_item]])',
            'KEY [[index_updated_at]] ([[updated_at]])',
        ], $this->tableOptions());

        $this->batchInsert(
            $table,
            require(__DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'news_columns.php'),
            require(__DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'news.php')
        );

        if (!empty($data)) {
            $authManager = Yii::$app->getAuthManager();

            foreach ($data as $row) {
                try {
                    if (!isset($row['active']) || !$row['active']) {
                        continue;
                    }

                    $this->insert($authManager->itemTable, [
                        'name' => $itemName = 'ACCESS_NEWS_' . $row['news_id'],
                        'type' => Item::TYPE_PERMISSION,
                        'description' => Yii::t(
                            ModuleHelper::ADMIN_NEWS,
                            'Access to news record "{secureItemDescriptionParam}"',
                            ['secureItemDescriptionParam' => $row['title']]
                        ),
                        'active' => 0,
                        'created_at' => time(),
                        'updated_at' => time(),
                    ]);

                    $this->insert($table, [
                        'id' => $row['news_id'],
                        'title' => $row['title'],
                        'slug' => $row['href_title'],
                        'content' => $row['text'],
                        'rbac_on' => 0,
                        'rbac_item' => $itemName,
                        'created_by' => 0,
                        'created_at' => time(),
                        'updated_at' => time(),
                    ]);

                    ActiveParam::updateAllCounters(
                        ['value' => 1],
                        ['or', ['name' => 'news_count'], ['name' => 'permissions_count']]
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
        // Convert down only within full migration circle (up+down or down+up).

        $this->createTable($this->oldTableName(), [
            'news_id' => $this->integer(11)->unsigned()->notNull() . " AUTO_INCREMENT PRIMARY KEY",
            'active' => $this->integer(11)->unsigned()->notNull()->defaultValue('1'),
            'title' => $this->string(100)->notNull(),
            'href_title' => $this->string(50)->notNull()->defaultValue('0'),
            'text' => $this->text()->notNull(),
            'add_dt' => $this->string(10),
            'deleted' => $this->integer(11)->unsigned()->notNull()->defaultValue('0'),
        ], $this->tableOptions());

        if (!empty($data)) {
            if ($migrationData = $this->getTmpData($this->oldTableName() . '_up')) {
                foreach ($migrationData as $row) {
                    try {
                        $this->insert($this->oldTableName(), $row);

                        ActiveParam::updateAllCounters(
                            ['value' => -1],
                            ['or', ['name' => 'news_count']/*, ['name' => 'permissions_count']*/]
                        );
                    } catch (\Exception $e) {
                        Yii::error($e, __METHOD__);
                    }
                }
            }
        }
    }
}
