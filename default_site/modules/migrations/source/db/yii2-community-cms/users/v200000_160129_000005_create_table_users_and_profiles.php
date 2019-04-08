<?php

use app\modules\migrations\components\ConvertMigrationAbstract;
use app\helpers\ModuleHelper;
use site\modules\users\models\User;
use site\modules\users\models\Profile;
use yii\db\Expression;

class v200000_160129_000005_create_table_users_and_profiles extends ConvertMigrationAbstract
{
    /** @inheritdoc */
    public $upTableRequired = false;

    protected function tableName()
    {
        return User::tableName();
    }

    protected function convertUp($data)
    {
        $this->createTable($this->tableName(), [
            'id'                   => $this->integer(11) . ' unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY',
            'username'             => $this->string(32)->notNull(),
            'email'                => $this->string(255)->notNull(),
            'password_hash'        => $this->string(60)->notNull(),
            'auth_key'             => $this->string(32)->notNull(),
            'unconfirmed_email'    => $this->string(255)->defaultValue(''),
            'flags'                => 'TINYINT(1) unsigned NOT NULL DEFAULT 0',
            'registration_ip'      => $this->string(45)->defaultValue(''),
            'confirmed_at'         => $this->integer(11) . ' unsigned NULL',
            'blocked_at'           => $this->integer(11) . ' unsigned NULL',
            'created_at'           => $this->integer(11) . ' unsigned NOT NULL',
            'updated_at'           => $this->integer(11) . ' unsigned NOT NULL',
            'activity_at'           => $this->integer(11) . ' unsigned NOT NULL',
            'UNIQUE KEY `index_unique_users_username` (`username`)',
            'UNIQUE KEY `index_unique_users_email` (`email`)',
            'KEY `index_updated_at` (`updated_at`)',
            'KEY `index_activity_at` (`activity_at`)',
        ], $this->tableOptions());

        $this->createTable(Profile::tableName(), [
            'user_id'        => $this->integer(11) . ' unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY',
            'name'           => $this->string(255)->defaultValue(''),
            'public_email'   => $this->string(255)->defaultValue(''),
            'gravatar_email' => $this->string(255)->defaultValue(''),
            'gravatar_id'    => $this->string(32)->defaultValue(''),
            'image_url'      => $this->string(255)->defaultValue(''),
            'location'       => $this->string(255)->defaultValue(''),
            'website'        => $this->string(255)->defaultValue(''),
            'bio'            => $this->text()->defaultValue(''),
            'CONSTRAINT `fk_user_profile` FOREIGN KEY (`user_id`) REFERENCES ' . $this->tableName() . ' (`id`) ON DELETE CASCADE'
        ], $this->tableOptions());

        if (!empty($data)) {
            $adminData = [];

            foreach ($data as $user) {
                if ($user['user_id'] < 2 || (!empty($adminData) && $user['email'] == $adminData['email'])) {
                    continue;
                }
                $isUsernameValid = preg_match(User::$usernameRegexp, $user['nick']);

                if (YII_ENV_PROD) {
                    if (empty($adminData) && (int)$user['role_id'] === 4) {
                        $adminData['login'] = $isUsernameValid ? $user['nick'] : 'admin';
                        $adminData['email'] = $user['email'];
                        // old engine 1.0 supports multiple emails, but in 2.0 email field is unique.
                        $this->delete($this->tableName(), ['=', 'email', $adminData['email']]);

                        continue;
                    }
                }

                if (!$isUsernameValid) {
                    continue;
                }
                try {
                    $this->insert($this->tableName(), [
                        'id' => $user['user_id'],
                        'username' => $user['nick'],
                        'email' => $user['email'],
                        'auth_key' => Yii::$app->security->generateRandomString(),
                        'registration_ip' => $user['reg_ip'],
                        'confirmed_at' => time(),
                        'created_at' => $user['reg_dt'],
                        'updated_at' => time(),
                    ]);
                    $this->insert(Profile::tableName(), ['user_id' => $user['user_id']]);
                } catch (\Exception $e) {
                    Yii::error($e, __METHOD__);
                }
            }

            if (YII_ENV_PROD) {
                if (empty($adminData)) {
                    $adminData = [
                        'login' => 'admin',
                        'email' => 'no-reply@domain.ltd'
                    ];
                }

                $filename = Yii::getAlias('@app/config/prod/yii2_community_cms_site/admin.php');
                $contents = file_get_contents($filename);
                $contents = str_replace('{yii2_community_cms_site_admin_login}', $adminData['login'], $contents);
                $contents = str_replace('{yii2_community_cms_site_admin_email}', $adminData['email'], $contents);
                $contents = str_replace('{yii2_community_cms_site_admin_password}', '', $contents);
                file_put_contents($filename, $contents);

                Yii::$app->params['yii2_community_cms_site_admin_login'] = $adminData['login'];
                Yii::$app->params['yii2_community_cms_site_admin_email'] = $adminData['email'];
                Yii::$app->params['yii2_community_cms_site_admin_password'] = '';
            }
        }
    }

    protected function convertDown($data)
    {
        // Warning: BROKEN prod/admin.php config!

        if ($this->getDb()->getTableSchema(Profile::tableName(), true)) {
            $this->dropTable(Profile::tableName());
        }

        $this->createTable($this->oldTableName(), [
            'user_id' => $this->integer(11)->unsigned()->notNull() . " AUTO_INCREMENT PRIMARY KEY",
            'nick' => $this->string(20)->notNull()->defaultValue(''),
            'pass' => $this->string(40)->notNull(),
            'role_id' => $this->integer(11)->unsigned()->notNull()->defaultValue('1'),
            'email' => $this->string(50)->notNull(),
            'avatar' => $this->string(200)->notNull()->defaultValue('default.png'),
            'reg_ip' => $this->string(20)->notNull()->defaultValue(''),
            'reg_dt' => $this->string(10)->notNull()->defaultValue(''),
            'UNIQUE KEY [[nick]] ([[nick]])',
        ], $this->tableOptions());

        $this->execute("SET SESSION sql_mode='NO_AUTO_VALUE_ON_ZERO'");
        $this->insert($this->oldTableName(), [
            'user_id' => 0,
            'nick' => Yii::t(ModuleHelper::USERS, 'Guest'),
        ]);
        $this->execute("SET SESSION sql_mode=''");

        $this->insert($this->oldTableName(), [
            'user_id' => 1,
            'nick' => 'Yii2 Community CMS Support',
            'email' => 'support@domain.ltd',
        ]);

        if (!empty($data)) {
            foreach ($data as $user) {
                if ($user['id'] < 2 || mb_strlen($user['username'], 'UTF-8') > 20) {
                    continue;
                }
                try {
                    $this->insert($this->oldTableName(), [
                        'user_id' => $user['id'],
                        'nick' => $user['username'],
                        'email' => $user['email'],
                        'reg_ip' => $user['registration_ip'],
                        'reg_dt' => $user['created_at'],
                    ]);
                } catch (\Exception $e) {
                    Yii::error($e, __METHOD__);
                }
            }
        }
    }
}
