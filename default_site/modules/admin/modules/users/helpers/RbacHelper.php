<?php

namespace admin\modules\users\helpers;

use Yii;
use app\helpers\ModuleHelper;

/**
 * Class RbacHelper
 *
 * @package admin\modules\users\helpers
 * @since   2.0.0
 */
class RbacHelper
{
    const ADMIN_ACCESS                   = 'ADMIN_ACCESS';
    const CAPTCHA_IGNORE                 = 'CAPTCHA_IGNORE';
    const FORUM_ACCESS                   = 'FORUM_ACCESS';
    const FORUM_POSTS_ADD                = 'FORUM_POSTS_ADD';
    const FORUM_POSTS_DELETE             = 'FORUM_POSTS_DELETE';
    const FORUM_POSTS_MIGRATE            = 'FORUM_POSTS_MIGRATE';
    const FORUM_POSTS_UPDATE             = 'FORUM_POSTS_UPDATE';
    const FORUM_MODERATOR                = 'FORUM_MODERATOR';
    const FORUM_SELF_POSTS_DELETE        = 'FORUM_SELF_POSTS_DELETE';
    const FORUM_SELF_POSTS_UPDATE        = 'FORUM_SELF_POSTS_UPDATE';
    const FORUM_SELF_TOPICS_DELETE       = 'FORUM_SELF_TOPICS_DELETE';
    const FORUM_SELF_TOPICS_UPDATE       = 'FORUM_SELF_TOPICS_UPDATE';
    const FORUM_TOPICS_ADD               = 'FORUM_TOPICS_ADD';
    const FORUM_TOPICS_DELETE            = 'FORUM_TOPICS_DELETE';
    const FORUM_TOPICS_MIGRATE           = 'FORUM_TOPICS_MIGRATE';
    const FORUM_TOPICS_OPEN_CLOSE        = 'FORUM_TOPICS_OPEN_CLOSE';
    const FORUM_TOPICS_UPDATE            = 'FORUM_TOPICS_UPDATE';
    const FORUM_USER                     = 'FORUM_USER';
    const FORUM_SECTIONS_ACCESS_CONTROL  = 'FORUM_SECTIONS_ACCESS_CONTROL';
    const FORUM_SUBFORUMS_ACCESS_CONTROL = 'FORUM_SUBFORUMS_ACCESS_CONTROL';
    const FORUM_TOPICS_ACCESS_CONTROL    = 'FORUM_TOPICS_ACCESS_CONTROL';
    const FORUM_POSTS_ACCESS_CONTROL     = 'FORUM_POSTS_ACCESS_CONTROL';
    const NEWS_ACCESS                    = 'NEWS_ACCESS';
    const NEWS_ADD                       = 'NEWS_ADD';
    const NEWS_COMMENTS_ADD              = 'NEWS_COMMENTS_ADD';
    const NEWS_COMMENTS_DELETE           = 'NEWS_COMMENTS_DELETE';
    const NEWS_COMMENTS_UPDATE           = 'NEWS_COMMENTS_UPDATE';
    const NEWS_DELETE                    = 'NEWS_DELETE';
    const NEWS_MODERATOR                 = 'NEWS_MODERATOR';
    const NEWS_SELF_COMMENTS_DELETE      = 'NEWS_SELF_COMMENTS_DELETE';
    const NEWS_SELF_COMMENTS_UPDATE      = 'NEWS_SELF_COMMENTS_UPDATE';
    const NEWS_SELF_DELETE               = 'NEWS_SELF_DELETE';
    const NEWS_SELF_UPDATE               = 'NEWS_SELF_UPDATE';
    const NEWS_UPDATE                    = 'NEWS_UPDATE';
    const NEWS_ACCESS_CONTROL            = 'NEWS_ACCESS_CONTROL';
    const NEWS_ACCESS_1                  = 'NEWS_ACCESS_1';     // permission for access pre-defined news record
    const PAGES_ACCESS                   = 'PAGES_ACCESS';
    const PAGES_ACCESS_CONTROL           = 'PAGES_ACCESS_CONTROL';
    const PAGES_ACCESS_1                 = 'PAGES_ACCESS_1';    // permission for access pre-defined page 'About us'
    const PLUGINS_ACCESS                 = 'PLUGINS_ACCESS';
    const PLUGINS_ADMIN                  = 'PLUGINS_ADMIN';
    const PLUGINS_REWARDS_ACCESS         = 'PLUGINS_REWARDS_ACCESS';
    const PLUGINS_REWARDS_ADMIN          = 'PLUGINS_REWARDS_ADMIN';
    const PLUGINS_REWARDS_ARBITER        = 'PLUGINS_REWARDS_ARBITER';
    const PLUGINS_REWARDS_MANAGER        = 'PLUGINS_REWARDS_MANAGER';
    const PLUGINS_REWARDS_SETUP          = 'PLUGINS_REWARDS_SETUP';
    const PLUGINS_WAREHOUSE_ACCESS       = 'PLUGINS_WAREHOUSE_ACCESS';
    const PLUGINS_WAREHOUSE_ADMIN        = 'PLUGINS_WAREHOUSE_ADMIN';
    const PLUGINS_WAREHOUSE_MANAGER      = 'PLUGINS_WAREHOUSE_MANAGER';
    const PLUGINS_WAREHOUSE_SETUP        = 'PLUGINS_WAREHOUSE_SETUP';
    const PLUGINS_WAREHOUSE_TREASURING   = 'PLUGINS_WAREHOUSE_TREASURING';
    const PLUGINS_CHAT_ACCESS            = 'PLUGINS_CHAT_ACCESS';
    const PLUGINS_CHAT_ADMIN             = 'PLUGINS_CHAT_ADMIN';
    const PLUGINS_CHAT_MODERATOR         = 'PLUGINS_CHAT_MODERATOR';
    const PLUGINS_EVENTS_ACCESS          = 'PLUGINS_EVENTS_ACCESS';
    const PLUGINS_EVENTS_ADMIN           = 'PLUGINS_EVENTS_ADMIN';
    const PLUGINS_EVENTS_ARCHIVE_ADMIN   = 'PLUGINS_EVENTS_ARCHIVE_ADMIN';
    const PLUGINS_EVENTS_MANAGER         = 'PLUGINS_EVENTS_MANAGER';
    const PLUGINS_EVENTS_SETUP           = 'PLUGINS_EVENTS_SETUP';
    const PLUGINS_EVENTS_STATS_ADMIN     = 'PLUGINS_EVENTS_STATS_ADMIN';

    public static function getDefaultUserRole()
    {
        if ($defaultRole = Yii::$app->getModule(ModuleHelper::USERS)->params['default_role']) {
            return Yii::createObject(
                [
                    'class' => 'admin\modules\users\components\Role',
                    'name'  => $defaultRole,
                ]
            );
        }
    }
}
