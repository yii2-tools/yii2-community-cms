<?php

use yii\db\Migration;
use site\modules\users\helpers\Password;
use site\modules\users\models\User;
use site\modules\users\models\Profile;

class v200000_160519_034038_create_super_admin_account extends Migration
{
    public function up()
    {
        $this->insert(User::tableName(), [
            'id' => 1,
            'username' => Yii::$app->params['yii2_community_cms_site_admin_login'],
            'email' => Yii::$app->params['yii2_community_cms_site_admin_email'],
            'password_hash' => Password::hash(Yii::$app->params['yii2_community_cms_site_admin_password']),
            'auth_key' => Yii::$app->security->generateRandomString(),
            'confirmed_at' => time(),
            'created_at' => time(),
        ]);

        $this->insert(Profile::tableName(), [
            'user_id' => 1,
        ]);
    }

    public function down()
    {
        $this->delete(Profile::tableName(), [Profile::primaryKey()[0] => 1]);
        $this->delete(User::tableName(), [User::primaryKey()[0] => 1]);
    }
}
