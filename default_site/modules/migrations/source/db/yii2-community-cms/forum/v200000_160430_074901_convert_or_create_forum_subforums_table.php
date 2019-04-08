<?php

use yii\tools\params\models\ActiveParam;
use app\helpers\ModuleHelper;
use app\modules\migrations\components\ConvertMigrationAbstract;
use site\modules\forum\models\Subforum;
use admin\modules\users\components\Item;

class v200000_160430_074901_convert_or_create_forum_subforums_table extends ConvertMigrationAbstract
{
    /** @inheritdoc */
    public $upTableRequired = false;

    protected function tableName()
    {
        return Subforum::tableName();
    }

    protected function oldTableName()
    {
        return 'forum_forums';
    }

    protected function convertUp($data)
    {
        $table = $this->tableName();

        $this->createTable($table, [
            'id' => $this->integer(11)->unsigned()->notNull() . ' AUTO_INCREMENT',
            'section_id' => $this->integer(11)->unsigned()->notNull(),
            'title' => $this->string(255)->notNull(),
            'slug' => $this->string(255)->notNull(),
            'description' => $this->string(400),
            'topics_num' => $this->integer(11)->unsigned()->notNull()->defaultValue(0),
            'posts_num' => $this->integer(11)->unsigned()->notNull()->defaultValue(0),
            'last_post_id' => $this->integer(11)->unsigned()->notNull()->defaultValue(0),
            'rbac_on' => "TINYINT unsigned NOT NULL DEFAULT '0'",
            'rbac_item' => $this->string(255),
            'position' => "TINYINT unsigned NOT NULL DEFAULT '0'",
            'created_at' => $this->integer(11)->unsigned()->notNull(),
            'updated_at' => $this->integer(11)->unsigned()->notNull(),
            'PRIMARY KEY ([[id]])',
            'UNIQUE KEY [[index_unique_forum_subforums_slug]] ([[slug]])',
            //'CONSTRAINT [[fk_section_subforum]] FOREIGN KEY ([[section_id]]) REFERENCES ' . Section::tableName() . ' ([[id]]) ON DELETE CASCADE',
            'KEY [[index_forum_subforums_position]] ([[position]])',
            'KEY [[index_forum_subforums_updated_at]] ([[updated_at]])',
        ], $this->tableOptions());

        if (!empty($data)) {
            $authManager = Yii::$app->getAuthManager();
            foreach ($data as $row) {
                try {
                    $this->insert($authManager->itemTable, [
                        'name' => $itemName = 'ACCESS_FORUM_SUBFORUMS_' . $row['forum_id'],
                        'type' => Item::TYPE_PERMISSION,
                        'description' => Yii::t(
                            ModuleHelper::ADMIN_FORUM,
                            'Access to subforum "{secureItemDescriptionParam}"',
                            ['secureItemDescriptionParam' => $row['forum_title']]
                        ),
                        'active' => 1,
                        'created_at' => time(),
                        'updated_at' => time(),
                    ]);

                    $this->insert($table, [
                        'id' => $row['forum_id'],
                        'section_id' => $row['forum_section_id'],
                        'title' => $row['forum_title'],
                        'slug' => $row['forum_href'],
                        'description' => $row['forum_descr'],
                        'topics_num' => $row['topics'],
                        'posts_num' => $row['posts'],
                        'last_post_id' => 0,
                        'rbac_on' => 1,
                        'rbac_item' => $itemName,
                        'position' => $row['pos'],
                        'created_at' => time(),
                        'updated_at' => time(),
                    ]);

                    ActiveParam::updateAllCounters(
                        ['value' => 1],
                        ['or', ['name' => 'forum_subforums_count'], ['name' => 'permissions_count']]
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
            'forum_id' => $this->integer(11)->unsigned()->notNull() . " AUTO_INCREMENT PRIMARY KEY",
            'forum_section_id' => $this->integer(11)->unsigned()->notNull(),
            'forum_title' => $this->string(50)->notNull(),
            'forum_descr' => $this->string(200)->notNull()->defaultValue('0'),
            'forum_href' => $this->string(50)->notNull(),
            'pos' => $this->integer(11)->unsigned()->notNull()->defaultValue('0'),
            'topics' => $this->integer(11)->unsigned()->notNull()->defaultValue('0'),
            'posts' => $this->integer(11)->unsigned()->notNull()->defaultValue('0'),
            'last_post_dt' => $this->string(10),
            'last_post_author_id' => $this->integer(11),
            'last_post_topic_id' => $this->integer(11),
            'UNIQUE KEY [[forum_href]] ([[forum_href]])',
        ], $this->tableOptions());

        if (!empty($data)) {
            foreach ($data as $row) {
                try {
                    $this->insert($this->oldTableName(), [
                        'forum_id' => $row['id'],
                        'forum_section_id' => $row['section_id'],
                        'forum_title' => $row['title'],
                        'forum_descr' => $row['description'],
                        'forum_href' => $row['slug'],
                        'pos' => $row['position'],
                        'topics' => $row['topics_num'],
                        'posts' => $row['posts_num'],
                    ]);

                    ActiveParam::updateAllCounters(
                        ['value' => -1],
                        ['or', ['name' => 'forum_subforums_count']/*, ['name' => 'permissions_count']*/]
                    );
                } catch (\Exception $e) {
                    Yii::error($e, __METHOD__);
                }
            }
        }
    }
}
