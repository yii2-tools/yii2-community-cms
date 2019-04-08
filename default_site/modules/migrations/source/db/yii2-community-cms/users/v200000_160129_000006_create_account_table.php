<?php

use yii\db\Schema;
use site\modules\users\components\Migration;
use site\modules\users\models\User;
use site\modules\users\models\SocialAccount;

class v200000_160129_000006_create_account_table extends Migration
{
    public function up()
    {
        $this->createTable(SocialAccount::tableName(), [
            'id'         => $this->integer(11) . ' unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY',
            'user_id'    => $this->integer(11) . ' unsigned NOT NULL',
            'username'   => $this->string(255),
            'email'      => $this->string(255),
            'provider'   => $this->string(255)->notNull(),
            'client_id'  => $this->string(255)->notNull(),
            'code'       => $this->string(32)->notNull(),
            'data'       => $this->text(),
            'created_at' => $this->integer(11) . ' unsigned NOT NULL',
            'updated_at' => $this->integer(11) . ' unsigned NOT NULL',
            'UNIQUE KEY `index_unique_provider_client_id` (`provider`,`client_id`)',
            'UNIQUE KEY `index_unique_users_social_accounts_code` (`code`)',
            'KEY `index_updated_at` (`updated_at`)',
            'CONSTRAINT `fk_users_social_accounts` FOREIGN KEY (`user_id`) REFERENCES ' . User::tableName() . ' (`id`) ON DELETE CASCADE'
        ], $this->tableOptions);
    }

    public function down()
    {
        $this->dropTable(SocialAccount::tableName());
    }
}
