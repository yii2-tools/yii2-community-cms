<?php

use yii\tools\params\models\ActiveParam;
use app\helpers\ModuleHelper;
use app\modules\migrations\components\ConvertMigrationAbstract;
use site\modules\forum\models\Topic;
use admin\modules\users\components\Item;

class v200000_160430_075025_convert_or_create_forum_topics_table extends ConvertMigrationAbstract
{
    /** @inheritdoc */
    public $upTableRequired = false;

    protected function tableName()
    {
        return Topic::tableName();
    }

    protected function convertUp($data)
    {
        $table = $this->tableName();

        $this->createTable($table, [
            'id' => $this->integer(11)->unsigned()->notNull() . ' AUTO_INCREMENT',
            'subforum_id' => $this->integer(11)->unsigned()->notNull(),
            'title' => $this->string(255)->notNull(),
            'slug' => $this->string(255)->notNull(),
            'description' => $this->string(400),
            'views_num' => $this->integer(11)->unsigned()->notNull()->defaultValue(0),
            'posts_num' => $this->integer(11)->unsigned()->notNull()->defaultValue(0),
            'is_closed' => "TINYINT unsigned NOT NULL DEFAULT '0'",
            'is_fixed' => "TINYINT unsigned NOT NULL DEFAULT '0'",
            'last_post_id' => $this->integer(11)->unsigned()->notNull()->defaultValue(0),
            'rbac_on' => "TINYINT unsigned NOT NULL DEFAULT '0'",
            'rbac_item' => $this->string(255),
            'created_by' => $this->integer(11)->unsigned()->notNull(),
            'updated_by' => $this->integer(11)->unsigned()->notNull()->defaultValue(0),
            'created_at' => $this->integer(11)->unsigned()->notNull(),
            'updated_at' => $this->integer(11)->unsigned()->notNull(),
            'PRIMARY KEY ([[id]])',
            'KEY [[index_forum_topics_is_fixed]] ([[is_fixed]])',
            'UNIQUE KEY [[index_unique_forum_topics_slug]] ([[slug]])',
            //'CONSTRAINT [[fk_subforum_topic]] FOREIGN KEY ([[subforum_id]]) REFERENCES ' . Subforum::tableName() . ' ([[id]]) ON DELETE CASCADE',
            'KEY [[index_forum_topics_updated_at]] ([[updated_at]])',
        ], $this->tableOptions());

        if (!empty($data)) {
            $authManager = Yii::$app->getAuthManager();
            foreach ($data as $row) {
                try {
                    $this->insert($authManager->itemTable, [
                        'name' => $itemName = 'ACCESS_FORUM_TOPICS_' . $row['forum_topic_id'],
                        'type' => Item::TYPE_PERMISSION,
                        'description' => Yii::t(
                            ModuleHelper::ADMIN_FORUM,
                            'Access to forum topic "{secureItemDescriptionParam}"',
                            ['secureItemDescriptionParam' => $row['topic_title']]
                        ),
                        'active' => 0,
                        'created_at' => time(),
                        'updated_at' => time(),
                    ]);

                    $this->insert($table, [
                        'id' => $row['forum_topic_id'],
                        'subforum_id' => $row['forum_id'],
                        'title' => $row['topic_title'],
                        'slug' => $row['forum_topic_id'] . '-' . $row['href_title'],
                        'description' => $row['topic_descr'],
                        'views_num' => $row['reads'],
                        'posts_num' => $row['posts'],
                        'is_closed' => $row['is_closed'],
                        'is_fixed' => $row['is_fixed'],
                        'last_post_id' => 0,
                        'rbac_on' => 0,
                        'rbac_item' => $itemName,
                        'created_by' => $row['owner_id'],
                        'created_at' => $row['add_dt'],
                        'updated_at' => time(),
                    ]);

                    ActiveParam::updateAllCounters(['value' => 1], ['name' => 'permissions_count']);
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
            'forum_topic_id' => $this->integer(11)->unsigned()->notNull() . " AUTO_INCREMENT PRIMARY KEY",
            'forum_id' => $this->integer(11)->unsigned()->notNull(),
            'topic_title' => $this->string(100)->notNull(),
            'topic_descr' => $this->string(200)->notNull()->defaultValue(''),
            'href_title' => $this->string(50),
            'owner_id' => $this->integer(11)->unsigned(),
            'is_closed' => $this->integer(11)->unsigned()->notNull()->defaultValue('0'),
            'is_fixed' => $this->integer(11)->unsigned()->notNull()->defaultValue('0'),
            'reads' => $this->integer(11)->unsigned()->notNull()->defaultValue('0'),
            'posts' => $this->integer(11)->unsigned()->notNull()->defaultValue('0'),
            'add_dt' => $this->string(10),
            'last_post_dt' => $this->string(10),
            'last_post_author_id' => $this->integer(11),
        ], $this->tableOptions());

        if (!empty($data)) {
            foreach ($data as $row) {
                try {
                    $this->insert($this->oldTableName(), [
                        'forum_topic_id' => $row['id'],
                        'forum_id' => $row['subforum_id'],
                        'topic_title' => $row['title'],
                        'topic_descr' => $row['description'],
                        'href_title' => $row['slug'],
                        'owner_id' => $row['created_by'],
                        'is_closed' => $row['is_closed'],
                        'is_fixed' => $row['is_fixed'],
                        'reads' => $row['views_num'],
                        'posts' => $row['posts_num'],
                        'add_dt' => $row['created_at'],
                    ]);
                } catch (\Exception $e) {
                    Yii::error($e, __METHOD__);
                }
            }
        }
    }
}
