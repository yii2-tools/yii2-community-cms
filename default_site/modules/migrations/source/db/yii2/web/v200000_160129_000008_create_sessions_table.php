<?php

use yii\db\Migration;
use yii\di\Instance;

class v200000_160129_000008_create_sessions_table extends Migration
{
    /**
     * @throws yii\base\InvalidConfigException
     * @return \yii\web\Session
     */
    protected function getSession()
    {
        return Instance::ensure(
            Yii::createObject(Yii::$app->params['default_class_session']),
            \yii\web\Session::className()
        );
    }

    public function up()
    {
        $session = $this->getSession();
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';

        $this->createTable($session->sessionTable, [
            'id' => 'BINARY(16) NOT NULL PRIMARY KEY DEFAULT \'\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\'',
            'expire' => $this->integer(11) . ' unsigned NOT NULL',
            'data' => 'LONGBLOB NULL',
            'UNIQUE KEY `index_unique_users_sessions_expire_id` (`expire`,`id`)',
            'KEY `index_users_sessions_expire` (`expire`)',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable($this->getSession()->sessionTable);
    }
}
