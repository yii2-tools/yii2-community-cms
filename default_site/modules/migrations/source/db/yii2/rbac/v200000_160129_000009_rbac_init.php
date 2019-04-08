<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

use yii\base\InvalidConfigException;
use yii\rbac\DbManager;

class v200000_160129_000009_rbac_init extends \yii\db\Migration
{
    /**
     * @throws yii\base\InvalidConfigException
     * @return DbManager
     */
    protected function getAuthManager()
    {
        $authManager = Yii::$app->getAuthManager();
        if (!$authManager instanceof DbManager) {
            throw new InvalidConfigException('You should configure "authManager"'
                . ' component to use database before executing this migration.');
        }
        return $authManager;
    }

    public function up()
    {
        $authManager = $this->getAuthManager();
        $this->db = $authManager->db;
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';

        $this->createTable($authManager->ruleTable, [
            'name' => $this->string(64)->notNull(),
            'data' => $this->text(),
            'created_at' => $this->integer(11) . ' unsigned NOT NULL',
            'updated_at' => $this->integer(11) . ' unsigned NOT NULL',
            'PRIMARY KEY (name)',
            'KEY `index_users_rbac_rules_updated_at` (`updated_at`)'
        ], $tableOptions);

        $this->createTable($authManager->itemTable, [
            'name' => $this->string(64)->notNull(),
            'type' => $this->integer()->notNull(),
            'description' => $this->text(),
            'rule_name' => $this->string(64),
            'data' => $this->text(),
            'active' => $this->integer(1)->unsigned()->notNull()->defaultValue(1),
            'created_at' => $this->integer(11) . ' unsigned NOT NULL',
            'updated_at' => $this->integer(11) . ' unsigned NOT NULL',
            'PRIMARY KEY (name)',
            'KEY `index_users_rbac_items_active` (`active`)',
            'KEY `index_users_rbac_items_type` (`type`)',
            'KEY `index_users_rbac_items_updated_at` (`updated_at`)',
            'CONSTRAINT `fk_users_rbac_items` FOREIGN KEY (`rule_name`) REFERENCES ' . $authManager->ruleTable .
                ' (`name`) ON DELETE SET NULL ON UPDATE CASCADE',
        ], $tableOptions);

        $this->createTable($authManager->itemChildTable, [
            'parent' => $this->string(64)->notNull(),
            'child' => $this->string(64)->notNull(),
            'PRIMARY KEY (parent, child)',
            'FOREIGN KEY (parent) REFERENCES ' . $authManager->itemTable . ' (name) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY (child) REFERENCES ' . $authManager->itemTable . ' (name) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);

        $this->createTable($authManager->assignmentTable, [
            'item_name' => $this->string(64)->notNull(),
            'user_id' => $this->string(64)->notNull(),
            'created_at' => $this->integer(11) . ' unsigned NOT NULL',
            'updated_at' => $this->integer(11) . ' unsigned NOT NULL',
            'PRIMARY KEY (item_name, user_id)',
            'KEY `index_users_rbac_assignments_updated_at` (`updated_at`)',
            'CONSTRAINT `fk_users_rbac_assignments` FOREIGN KEY (`item_name`) REFERENCES ' . $authManager->itemTable . ' (`name`) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);

        $this->batchInsert(
            $authManager->ruleTable,
            require(__DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'rules_columns.php'),
            require(__DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'rules.php')
        );

        $this->batchInsert(
            $authManager->itemTable,
            require(__DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'items_columns.php'),
            require(__DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'items.php')
        );

        $this->batchInsert(
            $authManager->itemChildTable,
            require(__DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'items_childs_columns.php'),
            require(__DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'items_childs.php')
        );
    }

    public function down()
    {
        $authManager = $this->getAuthManager();
        $this->db = $authManager->db;

        $this->dropTable($authManager->assignmentTable);
        $this->dropTable($authManager->itemChildTable);
        $this->dropTable($authManager->itemTable);
        $this->dropTable($authManager->ruleTable);
    }
}
