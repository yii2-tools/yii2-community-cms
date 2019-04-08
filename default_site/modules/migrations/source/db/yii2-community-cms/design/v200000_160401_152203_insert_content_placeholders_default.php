<?php

use yii\db\Migration;
use yii\db\Schema;
use app\helpers\ModuleHelper;
use design\modules\content\models\ActivePlaceholder;

class v200000_160401_152203_insert_content_placeholders_default extends Migration
{
    public function up()
    {
        $this->batchInsert(
            ActivePlaceholder::tableName(),
            [
                'name', 'type', 'content', 'status', 'created_at', 'updated_at',
            ],
            [
                ['ACCOUNT_FORM', 2, '@site/modules/users/views/placeholders/account_form.php', 1, time(), time()],
                ['ADMIN_SIDEBAR', 2, '@site/views/placeholders/admin_sidebar.php', 1, time(), time()],
                ['ADMIN_SIDEBAR_COLLAPSE', 1, 'return (($user = \Yii::$app->getUser()->getIdentity()) && $user->getIsAdmin() && $user->params[\'sidebar_collapse\']) ? \'sidebar-collapse\' : \'\';', 1, time(), time()],
                ['ADMIN_SIDEBAR_MINI', 1, 'return (($user = \Yii::$app->getUser()->getIdentity()) && $user->getIsAdmin()) ? \'sidebar-mini\' : \'\';', 1, time(), time()],
                ['ALERTS', 2, '@site/views/placeholders/alerts.php', 1, time(), time()],
                ['BREADCRUMBS', 2, '@site/views/placeholders/breadcrumbs.php', 1, time(), time()],
                ['PAGINATION', 2, '@site/views/placeholders/pagination.php', 1, time(), time()],
                ['LOGIN_FORM', 2, '@site/modules/users/views/placeholders/login_form.php', 1, time(), time()],
                ['MENU', 2, '@site/views/placeholders/menu.php', 1, time(), time()],
                ['PROFILE_FORM', 2, '@site/modules/users/views/placeholders/profile_form.php', 1, time(), time()],
                ['RECOVERY_FORM', 2, '@site/modules/users/views/placeholders/recovery_form.php', 1, time(), time()],
                ['RECOVERY_REQUEST_FORM', 2, '@site/modules/users/views/placeholders/recovery_request_form.php', 1, time(), time()],
                ['REGISTER_FORM', 2, '@site/modules/users/views/placeholders/register_form.php', 1, time(), time()],
                ['RESEND_FORM', 2, '@site/modules/users/views/placeholders/resend_form.php', 1, time(), time()],
                ['FORUM_POST_FORM', 2, '@site/modules/forum/views/placeholders/post_form.php', 1, time(), time()],
                ['FORUM_TOPIC_FORM', 2, '@site/modules/forum/views/placeholders/topic_form.php', 1, time(), time()],
                ['NEWS_FORM', 2, '@site/modules/news/views/placeholders/news_form.php', 1, time(), time()],
                ['SITE_TITLE', 1, 'return \Yii::$app->getModule(\app\helpers\ModuleHelper::DESIGN)->params[\'title\'];', 1, time(), time()],
                ['USER_SETTINGS_MENU', 2, '@site/modules/users/views/placeholders/user_settings_menu.php', 1, time(), time()],
                ['YEAR', 1, 'return date(\'Y\');', 1, time(), time()],
            ]
        );

        $on = ActivePlaceholder::relationTableRoutesOn();

        $this->batchInsert(
            ActivePlaceholder::tableNameRelationRoutes(),
            [
                $on[0], $on[1],
            ],
            [
                ['ACCOUNT_FORM', '%/%'],
                ['ADMIN_SIDEBAR', '%/%'],
                ['ADMIN_SIDEBAR_COLLAPSE', '%/%'],
                ['ADMIN_SIDEBAR_MINI', '%/%'],
                ['ALERTS', '%/%'],
                ['BREADCRUMBS', '%/%'],
                ['PAGINATION', '%/%'],
                ['LOGIN_FORM', '%/%'],
                ['MENU', '%/%'],
                ['PROFILE_FORM', '%/%'],
                ['RECOVERY_FORM', '%/%'],
                ['RECOVERY_REQUEST_FORM', '%/%'],
                ['REGISTER_FORM', '%/%'],
                ['RESEND_FORM', '%/%'],
                ['FORUM_POST_FORM', '%' . ModuleHelper::FORUM . '%'],
                ['FORUM_TOPIC_FORM', '%' . ModuleHelper::FORUM . '%'],
                ['NEWS_FORM', '%/%'],
                ['SITE_TITLE', '%/%'],
                ['USER_SETTINGS_MENU', '%/%'],
                ['YEAR', '%/%'],
            ]
        );
    }

    public function down()
    {
        $names = "
            'ACCOUNT_FORM', 'ADMIN_SIDEBAR', 'ADMIN_SIDEBAR_COLLAPSE', 'ADMIN_SIDEBAR_MINI', 'ALERTS',
            'BREADCRUMBS', 'PAGINATION', 'LOGIN_FORM', 'MENU', 'PROFILE_FORM', 'RECOVERY_FORM',
            'RECOVERY_REQUEST_FORM', 'REGISTER_FORM', 'RESEND_FORM', 'FORUM_POST_FORM', 'FORUM_TOPIC_FORM',
            'NEWS_FORM', 'SITE_TITLE', 'USER_SETTINGS_MENU', 'YEAR'
        ";

        $this->delete(ActivePlaceholder::tableName(), "[[name]] IN ($names)");

        $on = ActivePlaceholder::relationTableRoutesOn();

        $this->delete(ActivePlaceholder::tableNameRelationRoutes(), "[[{$on[0]}]] IN ($names)");
    }
}
