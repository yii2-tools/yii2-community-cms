<?php

use yii\helpers\Inflector;
use yii\tools\params\models\ActiveParam;
use app\helpers\ModuleHelper;
use app\helpers\RouteHelper;
use app\modules\migrations\components\ConvertMigrationAbstract;
use app\modules\routing\models\Route;
use site\modules\pages\models\Page;
use admin\modules\users\components\Item;

class v200000_160426_133248_convert_or_create_pages_table extends ConvertMigrationAbstract
{
    /** @inheritdoc */
    public $upTableRequired = false;

    public $oldRoutingTable = 'route';

    protected function tableName()
    {
        return Page::tableName();
    }

    protected function convertUp($data)
    {
        $table = $this->tableName();

        $this->createTable($table, [
            'id' => $this->integer(11)->unsigned()->notNull() . ' AUTO_INCREMENT',
            'title' => $this->string(255)->notNull(),
            'slug' => $this->string(255)->notNull(),
            'content' => 'LONGTEXT NOT NULL',
            'route_id' => $this->integer(11)->unsigned()->notNull(),
            'rbac_on' => "TINYINT unsigned NOT NULL DEFAULT '0'",
            'rbac_item' => $this->string(255),
            'created_at' => $this->integer(11)->unsigned()->notNull(),
            'updated_at' => $this->integer(11)->unsigned()->notNull(),
            'PRIMARY KEY ([[id]])',
            'UNIQUE KEY [[index_unique_pages_slug]] ([[slug]])',
            'UNIQUE KEY [[index_unique_pages_route_id]] ([[route_id]])',
            'UNIQUE KEY [[index_unique_pages_rbac_item]] ([[rbac_item]])',
            'KEY [[index_updated_at]] ([[updated_at]])',
        ], $this->tableOptions());

        $this->batchInsert(
            $table,
            require(__DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'pages_columns.php'),
            require(__DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'pages.php')
        );

        if (!empty($data)) {
            $oldRoutingTableSchema = $this->getDb()->getTableSchema($this->oldRoutingTable, true);
            $authManager = Yii::$app->getAuthManager();

            foreach ($data as $row) {
                try {
                    if (!isset($row['is_user_page']) || !$row['is_user_page']) {
                        continue;
                    }

                    // Determine page slug & route url.
                    if ($oldRoutingTableSchema) {
                        $oldRoute = $this->getDb()
                            ->createCommand(
                                'SELECT * FROM [[' . $this->oldRoutingTable . ']] WHERE route_id = :route_id',
                                [':route_id' => $row['route_id']]
                            )->queryOne();
                    }

                    $urlPattern = isset($oldRoute) ? $oldRoute['href'] : Inflector::slug($row['title']);

                    // Checks route url for unique.
                    if (Route::find()->urlPattern($urlPattern)->exists()) {
                        $urlPattern .= '-old-migrated';
                    }

                    $this->insert(Route::tableName(), [
                        'module' => ModuleHelper::PAGES,
                        'default_url_pattern' => $urlPattern,
                        'url_pattern' => $urlPattern,
                        'route' => RouteHelper::SITE_PAGES_SHOW,
                        'description' => Yii::t(
                            ModuleHelper::ADMIN_PAGES,
                            'Page "{routeDescriptionParam}"',
                            ['routeDescriptionParam' => $row['title']]
                        ),
                        'created_at' => time(),
                        'updated_at' => time(),
                    ]);
                    $routeId = $this->db->lastInsertID;

                    $this->insert($authManager->itemTable, [
                        'name' => $itemName = 'ACCESS_PAGES_' . $row['page_id'],
                        'type' => Item::TYPE_PERMISSION,
                        'description' => Yii::t(
                            ModuleHelper::ADMIN_PAGES,
                            'Access to page "{secureItemDescriptionParam}"',
                            ['secureItemDescriptionParam' => $row['title']]
                        ),
                        'active' => 1,
                        'created_at' => time(),
                        'updated_at' => time(),
                    ]);

                    $this->insert($table, [
                        'id' => $row['page_id'],
                        'title' => $row['title'],
                        'slug' => $urlPattern . '-' . time(),
                        'content' => $row['content'],
                        'route_id' => $routeId,
                        'rbac_on' => 1,
                        'rbac_item' => $itemName,
                        'created_at' => time(),
                        'updated_at' => time(),
                    ]);

                    ActiveParam::updateAllCounters(
                        ['value' => 1],
                        ['or', ['name' => 'pages_count'], ['name' => 'permissions_count']]
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
            'page_id' => $this->integer(11)->unsigned()->notNull() . " AUTO_INCREMENT PRIMARY KEY",
            'page_type_id' => $this->integer(11)->unsigned()->notNull()->defaultValue('1'),
            'route_id' => $this->integer(11)->unsigned()->notNull(),
            'is_user_page' => $this->integer(11)->unsigned()->notNull()->defaultValue('1'),
            'no_edit' => $this->integer(11)->unsigned()->notNull()->defaultValue('0'),
            'no_type_change' => $this->integer(11)->unsigned()->notNull()->defaultValue('0'),
            'active' => $this->integer(11)->unsigned()->notNull()->defaultValue('1'),
            'title' => $this->string(100)->notNull(),
            'content' => $this->text()->notNull(),
            'UNIQUE KEY [[href]] ([[route_id]])',
        ], $this->tableOptions());

        if (!empty($data)) {
            if ($migrationData = $this->getTmpData($this->oldTableName() . '_up')) {
                foreach ($migrationData as $row) {
                    try {
                        $this->insert($this->oldTableName(), $row);

                        ActiveParam::updateAllCounters(
                            ['value' => -1],
                            ['or', ['name' => 'pages_count']/*, ['name' => 'permissions_count']*/]
                        );
                    } catch (\Exception $e) {
                        Yii::error($e, __METHOD__);
                    }
                }
            }
        }
    }
}
