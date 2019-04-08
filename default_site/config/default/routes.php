<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 08.02.16 15:43
 */

use app\helpers\RouteHelper;

return [
    // admin
    'admin'                                     => RouteHelper::ADMIN_HOME,
    // admin/setup
    'admin/<module:\w+>/setup'                  => RouteHelper::ADMIN_SETUP,
    'admin/<module:\w+>/setup/update'           => RouteHelper::ADMIN_SETUP_UPDATE,
    // admin/users
    'admin/users'                               => RouteHelper::ADMIN_USERS_MANAGEMENT,
    'admin/users/roles'                         => RouteHelper::ADMIN_USERS_ROLES,
    'admin/users/permissions'                   => RouteHelper::ADMIN_USERS_PERMISSIONS,
    'admin/users/roles/<action>'                => RouteHelper::ADMIN_USERS_ROLES_ACTION,
    'admin/users/permissions/<action>'          => RouteHelper::ADMIN_USERS_PERMISSIONS_ACTION,
    'admin/users/profile/update'                => RouteHelper::ADMIN_USERS_MANAGEMENT_UPDATE_PROFILE,
    'admin/users/<action>'                      => RouteHelper::ADMIN_USERS_MANAGEMENT_ACTION,
    // admin/design
    'admin/design'                              => RouteHelper::ADMIN_DESIGN,
    'admin/design/menu'                         => RouteHelper::ADMIN_DESIGN_MENU,
    'admin/design/menu/items/<action>'          => RouteHelper::ADMIN_DESIGN_MENU_ITEMS_ACTION,
    'admin/design/packs'                        => RouteHelper::ADMIN_DESIGN_PACKS,
    'admin/design/packs/<action>'               => RouteHelper::ADMIN_DESIGN_PACKS_ACTION,
    // admin/pages
    'admin/pages'                               => RouteHelper::ADMIN_PAGES,
    'admin/pages/<action>'                      => RouteHelper::ADMIN_PAGES_ACTION,
    // admin/forum
    'admin/forum'                               => RouteHelper::ADMIN_FORUM,
    'admin/forum/section'                       => RouteHelper::ADMIN_FORUM_SECTIONS,
    'admin/forum/section/<action>'              => RouteHelper::ADMIN_FORUM_SECTIONS_ACTION,
    'admin/forum/subforum'                      => RouteHelper::ADMIN_FORUM_SUBFORUMS,
    'admin/forum/subforum/<action>'             => RouteHelper::ADMIN_FORUM_SUBFORUMS_ACTION,
    'admin/forum/topic/migrate'                 => RouteHelper::ADMIN_FORUM_TOPICS_MIGRATE,
    'admin/forum/post/migrate'                  => RouteHelper::ADMIN_FORUM_POSTS_MIGRATE,
    // admin/plugins
    'admin/plugins'                             => RouteHelper::ADMIN_PLUGINS_MANAGEMENT,
    'admin/plugins/<action>'                    => RouteHelper::ADMIN_PLUGINS_MANAGEMENT_ACTION,
    // admin/widgets
    'admin/widgets'                             => RouteHelper::ADMIN_WIDGETS_MANAGEMENT,
    'admin/widgets/<action>'                    => RouteHelper::ADMIN_WIDGETS_MANAGEMENT_ACTION,
    // site
    'captcha'                                   => RouteHelper::SITE_CAPTCHA,
    // site/users
    'settings/avatar-upload'                    => RouteHelper::SITE_USERS_SETTINGS_PROFILE_IMAGE_UPLOAD,
    // site/forum
    'forum/post/create/<topicId:\d+>'           => RouteHelper::SITE_FORUM_POSTS_CREATE,
    'forum/post/delete/<id:\d+>'                => RouteHelper::SITE_FORUM_POSTS_DELETE,
    'forum/topic/delete/<id:\d+>'               => RouteHelper::SITE_FORUM_TOPICS_DELETE,
    // site/news
    'news/delete/<id:\d+>'                      => RouteHelper::SITE_NEWS_DELETE,
    // site/plugins
    'plugin/api/request'                        => RouteHelper::SITE_PLUGINS_API_REQUEST,
];