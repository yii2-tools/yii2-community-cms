<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 29.04.16 14:42
 */

use app\helpers\ModuleHelper;
use app\helpers\RouteHelper;

return [
    [1000, ModuleHelper::SITE, '/', '/', '', RouteHelper::SITE_HOME, '{}', Yii::t(ModuleHelper::SITE, 'Main page'), time(), time()],

    [2000, ModuleHelper::USERS, 'profile/<username:.+>', 'profile/<username:.+>', '', RouteHelper::SITE_USERS_PROFILE_SHOW, '{}', Yii::t(ModuleHelper::USERS, 'Profile page'), time(), time()],
    [2100, ModuleHelper::USERS, 'login', 'login', '', RouteHelper::SITE_USERS_LOGIN, '{}', Yii::t(ModuleHelper::USERS, 'Login page'), time(), time()],
    //[2101, ModuleHelper::USERS, 'auth', 'auth', '', RouteHelper::SITE_USERS_AUTH, '{}', Yii::t(ModuleHelper::USERS, 'Social connect page'), time(), time()],
    [2102, ModuleHelper::USERS, 'logout', 'logout', '', RouteHelper::SITE_USERS_LOGOUT, '{}', Yii::t(ModuleHelper::USERS, 'Logout page'), time(), time()],
    [2200, ModuleHelper::USERS, 'register', 'register', '', RouteHelper::SITE_USERS_REGISTRATION_REGISTER, '{}', Yii::t(ModuleHelper::USERS, 'Registration page'), time(), time()],
    //[2201, ModuleHelper::USERS, 'connect', 'connect', '', RouteHelper::SITE_USERS_REGISTRATION_CONNECT, '{}', Yii::t(ModuleHelper::USERS, 'Registration via social networks'), time(), time()],
    [2202, ModuleHelper::USERS, 'confirm/resend', 'confirm/resend', '', RouteHelper::SITE_USERS_REGISTRATION_RESEND, '{}', Yii::t(ModuleHelper::USERS, 'Request confirmation link page'), time(), time()],
    [2203, ModuleHelper::USERS, 'register/confirm', 'register/confirm', '', RouteHelper::SITE_USERS_REGISTRATION_CONFIRM, '{}', Yii::t(ModuleHelper::USERS, 'Registration confirm page'), time(), time()],
    [2300, ModuleHelper::USERS, 'profile', 'profile', '', RouteHelper::SITE_USERS_SETTINGS_PROFILE, '{}', Yii::t(ModuleHelper::USERS, 'Profile settings page'), time(), time()],
    [2301, ModuleHelper::USERS, 'account', 'account', '', RouteHelper::SITE_USERS_SETTINGS_ACCOUNT, '{}', Yii::t(ModuleHelper::USERS, 'Private account settings page'), time(), time()],
    //[2302, ModuleHelper::USERS, 'networks', 'networks', '', RouteHelper::SITE_USERS_SETTINGS_NETWORKS, '{}', Yii::t(ModuleHelper::USERS, 'Social networks settings'), time(), time()],
    [2303, ModuleHelper::USERS, 'settings/confirm', 'settings/confirm', '', RouteHelper::SITE_USERS_SETTINGS_CONFIRM, '{}', Yii::t(ModuleHelper::USERS, 'Private account settings change confirm'), time(), time()],
    [2400, ModuleHelper::USERS, 'password/recovery', 'password/recovery', '', RouteHelper::SITE_USERS_RECOVERY_REQUEST, '{}', Yii::t(ModuleHelper::USERS, 'Forgot password page'), time(), time()],
    [2401, ModuleHelper::USERS, 'password/reset', 'password/reset', '', RouteHelper::SITE_USERS_RECOVERY_RESET, '{}', Yii::t(ModuleHelper::USERS, 'Password reset page'), time(), time()],

    [3000, ModuleHelper::PLUGINS, 'plugin/<name:[a-zA-Z0-9_-]+>', 'plugin/<name:[a-zA-Z0-9_-]+>', '', RouteHelper::SITE_PLUGINS_SHOW, '{}', Yii::t(ModuleHelper::PLUGINS, 'Plugin page'), time(), time()],

    [4000, ModuleHelper::FORUM, 'forum', 'forum', '', RouteHelper::SITE_FORUM, '{}', Yii::t(ModuleHelper::FORUM, 'Forum main page'), time(), time()],

    [4100, ModuleHelper::FORUM, 'forum/topic/create/<subforum_id:\d+>', 'forum/topic/create/<subforum_id:\d+>', '', RouteHelper::SITE_FORUM_TOPICS_CREATE, '{}', Yii::t(ModuleHelper::FORUM, 'Forum topic create page'), time(), time()],
    [4101, ModuleHelper::FORUM, 'forum/topic/edit/<id:\d+>', 'forum/topic/edit/<id:\d+>', '', RouteHelper::SITE_FORUM_TOPICS_UPDATE, '{}', Yii::t(ModuleHelper::FORUM, 'Forum topic update page'), time(), time()],
    [4200, ModuleHelper::FORUM, 'forum/post/edit/<id:\d+>', 'forum/post/edit/<id:\d+>', '', RouteHelper::SITE_FORUM_POSTS_UPDATE, '{}', Yii::t(ModuleHelper::FORUM, 'Forum post update page'), time(), time()],
    [4300, ModuleHelper::FORUM, 'forum/<section:[\w-]+>', 'forum/<section:[\w-]+>', '', RouteHelper::SITE_FORUM_SECTIONS_SHOW, '{}', Yii::t(ModuleHelper::FORUM, 'Forum section page'), time(), time()],
    [4301, ModuleHelper::FORUM, 'forum/<section:[\w-]+>/<subforum:[\w-]+>', 'forum/<section:[\w-]+>/<subforum:[\w-]+>', '', RouteHelper::SITE_FORUM_SUBFORUMS_SHOW, '{}', Yii::t(ModuleHelper::FORUM, 'Forum subforum page'), time(), time()],
    [4302, ModuleHelper::FORUM, 'forum/<section:[\w-]+>/<subforum:[\w-]+>/<topic:[\w-]+>', 'forum/<section:[\w-]+>/<subforum:[\w-]+>/<topic:[\w-]+>', '', RouteHelper::SITE_FORUM_TOPICS_SHOW, '{}', Yii::t(ModuleHelper::FORUM, 'Forum topic page'), time(), time()],

    [5000, ModuleHelper::NEWS, 'news', 'news', '', RouteHelper::SITE_NEWS, '{}', Yii::t(ModuleHelper::NEWS, 'News page'), time(), time()],
    [5001, ModuleHelper::NEWS, 'news/create', 'news/create', '', RouteHelper::SITE_NEWS_CREATE, '{}', Yii::t(ModuleHelper::NEWS, 'News create page'), time(), time()],
    [5002, ModuleHelper::NEWS, 'news/update/<id:\d+>', 'news/update/<id:\d+>', '', RouteHelper::SITE_NEWS_UPDATE, '{}', Yii::t(ModuleHelper::NEWS, 'News update page'), time(), time()],
    [5003, ModuleHelper::NEWS, 'news/<id:\d+>-<slug:[\w-]+>', 'news/<id:\d+>-<slug:[\w-]+>', '', RouteHelper::SITE_NEWS_SHOW, '{}', Yii::t(ModuleHelper::NEWS, 'News show page'), time(), time()],

    [100000, ModuleHelper::PAGES, 'about', 'about', '', RouteHelper::SITE_PAGES_SHOW, '{}', Yii::t(ModuleHelper::ADMIN_PAGES, 'Page "{routeDescriptionParam}"', ['routeDescriptionParam' => Yii::t(ModuleHelper::ADMIN_PAGES, 'About us')]), time(), time()],
];
