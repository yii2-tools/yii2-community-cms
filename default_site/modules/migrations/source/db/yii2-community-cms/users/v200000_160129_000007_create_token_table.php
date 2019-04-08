<?php

use yii\db\Schema;
use site\modules\users\components\Migration;
use site\modules\users\models\User;
use site\modules\users\models\Token;

class v200000_160129_000007_create_token_table extends Migration
{
    public function up()
    {
        $this->createTable(Token::tableName(), [
            'user_id' => Schema::TYPE_INTEGER . '(11) unsigned NOT NULL',
            'code' => Schema::TYPE_STRING . '(32) NOT NULL',
            'created_at' => Schema::TYPE_INTEGER . '(11) unsigned NOT NULL',
            'type' => 'TINYINT(1) unsigned NOT NULL',
            'UNIQUE KEY `index_unique_users_tokens_user_id_code_type` (`user_id`,`code`,`type`)',
            'CONSTRAINT `fk_users_tokens` FOREIGN KEY (`user_id`) REFERENCES ' . User::tableName() . ' (`id`) ON DELETE CASCADE',
        ], $this->tableOptions);
    }

    public function down()
    {
        $this->dropTable(Token::tableName());
    }
}
