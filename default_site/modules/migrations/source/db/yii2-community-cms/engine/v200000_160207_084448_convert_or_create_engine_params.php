<?php

use yii\helpers\ArrayHelper;
use yii\tools\params\models\ActiveParam;
use app\helpers\ModuleHelper as M;
use app\modules\migrations\components\ConvertMigrationAbstract;

class v200000_160207_084448_convert_or_create_engine_params extends ConvertMigrationAbstract
{
    protected $tableSiteEngine = '{{site_engine}}';
    protected $tableSiteSetup = '{{site_setup}}';
    protected $tableForumStatistic = '{{forum_statistic}}';
    protected $tableTeamInfo = '{{team_info}}';

    public function init()
    {
        parent::init();
        $this->upTableRequired = false;
    }

    protected function tableName()
    {
        return ActiveParam::tableName();
    }

    protected function oldTableName()
    {
        return '{{site_params}}';
    }

    protected function convertUp($data)
    {
        $this->createTable($this->tableName(), [
            'name' => $this->string(255)->notNull(),
            'value' => $this->string(255),
            'category' => $this->string(255),
            'created_at' => $this->integer(11) . ' unsigned NOT NULL',
            'updated_at' => $this->integer(11) . ' unsigned NOT NULL',
            'PRIMARY KEY ([[name]], [[category]])',
            'KEY `index_params_category` ([[category]])',
            'KEY `index_params_category_updated_at` ([[category]], [[updated_at]])',
        ], $this->tableOptions());

        $teamInfo = $forumStatistic = $siteSetup = $siteEngine = [];

        // Actual converting needed.
        if (!empty($data)) {
            if ($this->getDb()->getTableSchema($this->tableTeamInfo, true)) {
                $teamInfo = $this->getDb()->createCommand('SELECT * FROM ' . $this->tableTeamInfo)->queryOne();
                $this->setTmpData($this->tableTeamInfo, $teamInfo);
                $this->dropTable($this->tableTeamInfo);
            }

            if ($this->getDb()->getTableSchema($this->tableForumStatistic, true)) {
                $forumStatistic = $this->getDb()->createCommand('SELECT * FROM ' . $this->tableForumStatistic)->queryOne();
                $this->setTmpData($this->tableForumStatistic, $forumStatistic);
                $this->dropTable($this->tableForumStatistic);
            }

            if ($this->getDb()->getTableSchema($this->tableSiteSetup, true)) {
                $siteSetup = $this->getDb()->createCommand('SELECT * FROM ' . $this->tableSiteSetup)->queryOne();
                $this->setTmpData($this->tableSiteSetup, $siteSetup);
                $this->dropTable($this->tableSiteSetup);
            }

            if ($this->getDb()->getTableSchema($this->tableSiteEngine, true)) {
                $siteEngine = $this->getDb()->createCommand('SELECT * FROM ' . $this->tableSiteEngine)->queryOne();
                $this->setTmpData($this->tableSiteEngine, $siteEngine);
                $this->dropTable($this->tableSiteEngine);
            }

            // site config migration
            if (YII_ENV_PROD) {
                $filenameOld = Yii::getAlias('@app/system/domain.sys');
                $data[0]['domain'] = file_exists($filenameOld)
                    ? trim(file_get_contents($filenameOld))
                    : 'domain.ltd';

                $filename = Yii::getAlias('@app/config/prod/yii2_community_cms_site/site.php');
                $contents = file_get_contents($filename);
                $contents = str_replace('{yii2_community_cms_site_id}', $data[0]['site_id'], $contents);
                $contents = str_replace('{yii2_community_cms_site_key}', $data[0]['site_key'], $contents);
                $contents = str_replace('{yii2_community_cms_site_domain}', $data[0]['domain'], $contents);
                if (false !== file_put_contents($filename, $contents)) {
                    unlink($filenameOld);
                }

                Yii::$app->params['yii2_community_cms_site_id'] = $data[0]['site_id'];
                Yii::$app->params['yii2_community_cms_site_key'] = $data[0]['site_key'];
                Yii::$app->params['yii2_community_cms_site_domain'] = $data[0]['domain'];
                Yii::$app->params['yii2_community_cms_redis_db'] = Yii::$app->params['yii2_community_cms_site_id'];
                if (Yii::$app->has('redis')) {
                    Yii::$app->redis->database = Yii::$app->params['yii2_community_cms_redis_db'];
                }
                Yii::$app->runtimePath = Yii::$app->params['yii2_community_cms_logs']
                    . implode(DIRECTORY_SEPARATOR, ['', YII_APP_ID, Yii::$app->params['yii2_community_cms_site_domain'], 'runtime']);
            }
        }

        $data['teamInfo'] = $teamInfo;
        $data['forumStatistic'] = $forumStatistic;
        $data['siteSetup'] = $siteSetup;
        $data['siteEngine'] = $siteEngine;

        // this array contains only data that can be migrated from old engine.
        $params = [
            'title' => ArrayHelper::getValue($data['siteSetup'], 'main_title', Yii::t(M::SITE, 'Team site')),
            'copyright' => ArrayHelper::getValue($data['siteSetup'], 'footer_data', Yii::t(M::SITE, 'Copyright © {Team name}, {сity}')),
            'disk_space' => ArrayHelper::getValue($data['siteSetup'], 'disk_space_size', 10485760),
            'users_count' => ArrayHelper::getValue($data['forumStatistic'], 'users', 1),
            'last_user' => ArrayHelper::getValue($data['forumStatistic'], 'last_user_nick', ArrayHelper::getValue(Yii::$app->params, 'yii2_community_cms_site_admin_login', 'admin')),
            'forum_active' => ArrayHelper::getValue($data['siteSetup'], 'forum_active', 1),
            'forum_topics_count' => ArrayHelper::getValue($data['forumStatistic'], 'topics', 0),
            'forum_posts_count' => ArrayHelper::getValue($data['forumStatistic'], 'posts', 0),
            'forum_sections_count' => 0,
            'forum_subforums_count' => 0,
            'news_count' => 1,
            'pages_count' => 1,
        ];

        /**
         * Если изменяется, поменять также фикстуры (желательно, но не обязательно)
         */
        $this->batchInsert(
            $this->tableName(),
            [
                'name', 'value', 'category', 'created_at', 'updated_at'
            ],
            [
                ['title', $params['title'], M::DESIGN, time(), time()],
                ['copyright', $params['copyright'], M::DESIGN, time(), time()],
                ['forum_active', $params['forum_active'], M::FORUM, time(), time()],
                ['forum_topics_count', $params['forum_topics_count'], M::FORUM, time(), time()],
                ['forum_posts_count', $params['forum_posts_count'], M::FORUM, time(), time()],
                ['forum_sections_count', $params['forum_sections_count'], M::FORUM, time(), time()],
                ['forum_subforums_count', $params['forum_subforums_count'], M::FORUM, time(), time()],
                ['disk_space', $params['disk_space'], M::SITE, time(), time()],
                ['users_count', $params['users_count'], M::USERS, time(), time()],
                ['default_role', Yii::t(M::ADMIN_USERS, 'User'), M::USERS, time(), time()],
                ['guest_role', Yii::t(M::ADMIN_USERS, 'Guest'), M::USERS, time(), time()],
                ['last_user', $params['last_user'], M::USERS, time(), time()],
                ['roles_count', 8, M::USERS, time(), time()],
                ['permissions_count', 53, M::USERS, time(), time()],
                ['news_count', $params['news_count'], M::NEWS, time(), time()],
                ['pages_count', $params['pages_count'], M::PAGES, time(), time()],
            ]
        );
    }

    protected function convertDown($data)
    {
        $this->createTable($this->oldTableName(), [
            'site_id' => $this->integer(11) . " unsigned NOT NULL",
            'site_key' => $this->string(32)->notNull()->defaultValue('')
        ], $this->tableOptions());

        if ($this->getDb()->getTableSchema($this->tableTeamInfo, true)) {
            $this->dropTable($this->tableTeamInfo);
        }
        $this->createTable($this->tableTeamInfo, [
            'team_info_id' => $this->integer(11) . ' unsigned NOT NULL',
            'name' => $this->string(50)->notNull()->defaultValue(''),
            'descr' => $this->string(50),
        ], $this->tableOptions());

        if ($this->getDb()->getTableSchema($this->tableForumStatistic, true)) {
            $this->dropTable($this->tableForumStatistic);
        }
        $this->createTable($this->tableForumStatistic, [
            'forum_statistic_id' => $this->integer(11) . ' unsigned NOT NULL AUTO_INCREMENT',
            'topics' => $this->integer(11) . " unsigned NOT NULL DEFAULT '0'",
            'posts' => $this->integer(11) . " unsigned NOT NULL DEFAULT '0'",
            'users' => $this->integer(11) . " unsigned NOT NULL DEFAULT '0'",
            'last_user_nick' => $this->string(20)->notNull()->defaultValue('-'),
            'PRIMARY KEY (`forum_statistic_id`)'
        ], $this->tableOptions());

        if ($this->getDb()->getTableSchema($this->tableSiteSetup, true)) {
            $this->dropTable($this->tableSiteSetup);
        }
        $this->createTable($this->tableSiteSetup, [
            'site_setup_id' => $this->integer(11) . ' unsigned NOT NULL',
            'main_title' => $this->string(50)->notNull()->defaultValue('0'),
            'header_banner' => "TINYINT(1) unsigned NOT NULL DEFAULT '1'",
            'header_offset' => "TINYINT(1) unsigned NOT NULL DEFAULT '4'",
            'forum_active' => "TINYINT(1) unsigned NOT NULL DEFAULT '1'",
            'menu_pos' => "TINYINT(1) unsigned NOT NULL DEFAULT '1'",
            'menu_style' => "TINYINT(1) unsigned NOT NULL DEFAULT '1'",
            'footer_data' => $this->string(100)->notNull()->defaultValue('0'),
            'container_left_active' => "TINYINT(1) unsigned NOT NULL DEFAULT '0'",
            'container_right_active' => "TINYINT(1) unsigned NOT NULL DEFAULT '1'",
            'container_left_span' => "TINYINT(1) unsigned NOT NULL DEFAULT '3'",
            'container_right_span' => "TINYINT(1) unsigned NOT NULL DEFAULT '3'",
            'body_content_span' => "TINYINT(1) unsigned NOT NULL DEFAULT '0'",
            'disk_space_size' => $this->integer(10) . " unsigned NOT NULL DEFAULT '10485760'",
            'main_page_type' => "TINYINT(1) unsigned NOT NULL DEFAULT '0'",
        ], $this->tableOptions());

        if ($this->getDb()->getTableSchema($this->tableSiteEngine, true)) {
            $this->dropTable($this->tableSiteEngine);
        }
        $this->createTable($this->tableSiteEngine, [
            'site_engine_id' => $this->integer(11) . " unsigned NOT NULL DEFAULT '1'",
            'current_version' => $this->integer(11) . " unsigned NOT NULL DEFAULT '1'",
            'last_version' => $this->integer(11) . " unsigned NOT NULL DEFAULT '1'",
            'updates_available' => "TINYINT(1) unsigned NOT NULL DEFAULT '0'",
            'status' => "TINYINT(1) unsigned NOT NULL DEFAULT '1'",
            'current_title' => $this->string(50)->notNull()->defaultValue('0'),
            'current_descr' => $this->text()->notNull(),
            'last_title' => $this->string(50)->notNull()->defaultValue('0'),
            'last_descr' => $this->text()->notNull(),
            'release_dt' => $this->integer(11) . ' unsigned NOT NULL',
            'PRIMARY KEY (`site_engine_id`)'
        ]);

        if (!empty($data)) {
            $params = ArrayHelper::map($data, 'name', 'value');

            if ($teamInfo = $this->getTmpData($this->tableTeamInfo)) {
                $this->insert($this->tableTeamInfo, $teamInfo);
            }

            if ($forumStatistic = $this->getTmpData($this->tableForumStatistic)) {
                $this->insert($this->tableForumStatistic, $forumStatistic);
            }

            if ($siteSetup = $this->getTmpData($this->tableSiteSetup)) {
                $this->insert($this->tableSiteSetup, $siteSetup);
            }

            if ($siteEngine = $this->getTmpData($this->tableSiteEngine)) {
                $this->insert($this->tableSiteEngine, $siteEngine);
            }

            $this->insert($this->oldTableName(), [
                'site_id' => ArrayHelper::getValue(
                    $params,
                    'ID',
                    ArrayHelper::getValue(Yii::$app->params, 'yii2_community_cms_site_id', '0')
                ),
                'site_key' => ArrayHelper::getValue(
                    $params,
                    'KEY',
                    ArrayHelper::getValue(Yii::$app->params, 'yii2_community_cms_site_key', 'none')
                ),
            ]);
        }
    }
}
