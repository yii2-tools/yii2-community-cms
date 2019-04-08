<?php

use yii\tools\params\models\ActiveParam;
use app\helpers\ModuleHelper;
use app\modules\migrations\components\ConvertMigrationAbstract;
use site\modules\forum\models\Post;
use admin\modules\users\components\Item;

class v200000_160430_075030_convert_or_create_forum_posts_table extends ConvertMigrationAbstract
{
    /** @inheritdoc */
    public $upTableRequired = false;

    protected function tableName()
    {
        return Post::tableName();
    }

    protected function oldTableName()
    {
        return 'forum_messages';
    }

    protected function convertUp($data)
    {
        $table = $this->tableName();

        $this->createTable($table, [
            'id' => $this->integer(11)->unsigned()->notNull() . ' AUTO_INCREMENT',
            'topic_id' => $this->integer(11)->unsigned()->notNull(),
            'content' => 'LONGTEXT NOT NULL',
            'is_first' => "TINYINT unsigned NOT NULL DEFAULT '0'",
            'rbac_on' => "TINYINT unsigned NOT NULL DEFAULT '0'",
            'rbac_item' => $this->string(255),
            'created_by' => $this->integer(11)->unsigned()->notNull(),
            'updated_by' => $this->integer(11)->unsigned()->notNull()->defaultValue(0),
            'created_at' => $this->integer(11)->unsigned()->notNull(),
            'updated_at' => $this->integer(11)->unsigned()->notNull(),
            'PRIMARY KEY ([[id]])',
            //'CONSTRAINT [[fk_topic_post]] FOREIGN KEY ([[topic_id]]) REFERENCES ' . Topic::tableName() . ' ([[id]]) ON DELETE CASCADE',
            'KEY [[index_forum_posts_updated_at]] ([[updated_at]])',
        ], $this->tableOptions());

        if (!empty($data)) {
            $authManager = Yii::$app->getAuthManager();
            foreach ($data as $row) {
                try {
                    $this->insert($authManager->itemTable, [
                        'name' => $itemName = 'ACCESS_FORUM_POSTS_' . $row['forum_message_id'],
                        'type' => Item::TYPE_PERMISSION,
                        'description' => Yii::t(
                            ModuleHelper::ADMIN_FORUM,
                            'Access to forum post "{secureItemDescriptionParam}"',
                            ['secureItemDescriptionParam' => $row['forum_message_id']]
                        ),
                        'active' => 0,
                        'created_at' => time(),
                        'updated_at' => time(),
                    ]);

                    $this->insert($table, [
                        'id' => $row['forum_message_id'],
                        'topic_id' => $row['forum_topic_id'],
                        'content' => $row['message_text'],
                        'is_first' => $row['is_first'],
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
            'forum_message_id' => $this->integer(11)->unsigned()->notNull() . " AUTO_INCREMENT PRIMARY KEY",
            'forum_topic_id' => $this->integer(11)->unsigned()->notNull(),
            'message_text' => $this->text()->notNull(),
            'is_first' => $this->integer(11)->notNull()->defaultValue('0'),
            'owner_id' => $this->integer(11)->unsigned(),
            'last_editor_id' => $this->integer(11)->unsigned()->defaultValue('0'),
            'last_edit_dt' => $this->string(10)->defaultValue('0'),
            'add_dt' => $this->string(10),
        ], $this->tableOptions());

        if (!empty($data)) {
            foreach ($data as $row) {
                try {
                    $this->insert($this->oldTableName(), [
                        'forum_message_id' => $row['id'],
                        'forum_topic_id' => $row['topic_id'],
                        'message_text' => $row['content'],
                        'is_first' => $row['is_first'],
                        'owner_id' => $row['created_by'],
                        'add_dt' => $row['created_at'],
                    ]);
                } catch (\Exception $e) {
                    Yii::error($e, __METHOD__);
                }
            }
        }
    }
}
