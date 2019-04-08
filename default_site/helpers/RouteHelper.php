<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 28.01.16 9:28
 */

namespace app\helpers;

/**
 * All available site routes for current engine version.
 * Route - internal address of application action
 * Example: <module-parent>[/<module-child> ...]/<controller>/<action>
 *
 * WARNING! Use only php function in this class
 *
 * Class RouteHelper
 * @package app\helpers
 * @since 2.0.0
 */
class RouteHelper extends BaseHelper
{
    //////////////
    // dev/test //
    //////////////

    const DEBUG_HOME                                = '/debug/default/index';
    const DEBUG_CONTROLLER_ACTION                   = '/debug/<controller>/<action>';

    const GII_HOME                                  = '/gii/default/index';
    const GII_VIEW                                  = '/gii/default/view';
    const GII_CONTROLLER_ACTION                     = '/gii/<controller>/<action>';

    ///////////
    // admin //
    ///////////

    const ADMIN_HOME                                = '/admin/default/index';
    const ADMIN_ERROR                               = '/admin/default/error';

    // admin/setup
    const ADMIN_SETUP                               = '/admin/setup/default/index';
    const ADMIN_SETUP_UPDATE                        = '/admin/setup/default/update';

    // admin/users
    const ADMIN_USERS_MANAGEMENT                    = '/admin/users/management/index';
    const ADMIN_USERS_MANAGEMENT_ACTION             = '/admin/users/management/<action>';
    const ADMIN_USERS_MANAGEMENT_CREATE             = '/admin/users/management/create';
    const ADMIN_USERS_MANAGEMENT_UPDATE             = '/admin/users/management/update';
    const ADMIN_USERS_MANAGEMENT_UPDATE_PROFILE     = '/admin/users/management/update-profile';
    const ADMIN_USERS_MANAGEMENT_INFO               = '/admin/users/management/info';
    const ADMIN_USERS_MANAGEMENT_ASSIGNMENTS        = '/admin/users/management/assignments';
    const ADMIN_USERS_MANAGEMENT_CONFIRM            = '/admin/users/management/confirm';
    const ADMIN_USERS_MANAGEMENT_BLOCK              = '/admin/users/management/block';
    const ADMIN_USERS_MANAGEMENT_DELETE             = '/admin/users/management/delete';
    const ADMIN_USERS_ROLES                         = '/admin/users/roles/index';
    const ADMIN_USERS_ROLES_CONTROLLER              = '/admin/users/roles';
    const ADMIN_USERS_ROLES_ACTION                  = '/admin/users/roles/<action>';
    const ADMIN_USERS_ROLES_CREATE                  = '/admin/users/roles/create';
    const ADMIN_USERS_ROLES_UPDATE                  = '/admin/users/roles/update';
    const ADMIN_USERS_ROLES_DELETE                  = '/admin/users/roles/delete';
    const ADMIN_USERS_PERMISSIONS                   = '/admin/users/permissions/index';
    const ADMIN_USERS_PERMISSIONS_CONTROLLER        = '/admin/users/permissions';
    const ADMIN_USERS_PERMISSIONS_ACTION            = '/admin/users/permissions/<action>';
    const ADMIN_USERS_PERMISSIONS_CREATE            = '/admin/users/permissions/create';
    const ADMIN_USERS_PERMISSIONS_UPDATE            = '/admin/users/permissions/update';
    const ADMIN_USERS_PERMISSIONS_DELETE            = '/admin/users/permissions/delete';

    // admin/design
    const ADMIN_DESIGN                              = '/admin/design/default/index';  // alias to ADMIN_DESIGN_PACKS
    const ADMIN_DESIGN_MENU                         = '/admin/design/menu/default/index';
    const ADMIN_DESIGN_MENU_ITEMS_ACTION            = '/admin/design/menu/items/<action>';
    const ADMIN_DESIGN_MENU_ITEMS_POSITION          = '/admin/design/menu/items/position';
    const ADMIN_DESIGN_MENU_ITEMS_CREATE            = '/admin/design/menu/items/create';
    const ADMIN_DESIGN_MENU_ITEMS_UPDATE            = '/admin/design/menu/items/update';
    const ADMIN_DESIGN_MENU_ITEMS_DELETE            = '/admin/design/menu/items/delete';
    const ADMIN_DESIGN_PACKS                        = '/admin/design/packs/default/index';
    const ADMIN_DESIGN_PACKS_ACTION                 = '/admin/design/packs/default/<action>';
    const ADMIN_DESIGN_PACKS_IMPORT                 = '/admin/design/packs/default/import';
    const ADMIN_DESIGN_PACKS_EXPORT                 = '/admin/design/packs/default/export';
    const ADMIN_DESIGN_PACKS_EDIT                   = '/admin/design/packs/default/edit';
    const ADMIN_DESIGN_PACKS_DELETE                 = '/admin/design/packs/default/delete';

    // admin/pages
    const ADMIN_PAGES                               = '/admin/pages/default/index';
    const ADMIN_PAGES_ACTION                        = '/admin/pages/default/<action>';
    const ADMIN_PAGES_CREATE                        = '/admin/pages/default/create';
    const ADMIN_PAGES_UPDATE                        = '/admin/pages/default/update';
    const ADMIN_PAGES_DELETE                        = '/admin/pages/default/delete';

    // admin/forum
    const ADMIN_FORUM                               = '/admin/forum/default/index';  // alias to ADMIN_FORUM_SECTIONS
    const ADMIN_FORUM_SECTIONS                      = '/admin/forum/section/index';
    const ADMIN_FORUM_SECTIONS_ACTION               = '/admin/forum/section/<action>';
    const ADMIN_FORUM_SECTIONS_POSITION             = '/admin/forum/section/position';
    const ADMIN_FORUM_SECTIONS_CREATE               = '/admin/forum/section/create';
    const ADMIN_FORUM_SECTIONS_UPDATE               = '/admin/forum/section/update';
    const ADMIN_FORUM_SECTIONS_DELETE               = '/admin/forum/section/delete';
    const ADMIN_FORUM_SUBFORUMS                     = '/admin/forum/subforum/index';
    const ADMIN_FORUM_SUBFORUMS_ACTION              = '/admin/forum/subforum/<action>';
    const ADMIN_FORUM_SUBFORUMS_POSITION            = '/admin/forum/subforum/position';
    const ADMIN_FORUM_SUBFORUMS_CREATE              = '/admin/forum/subforum/create';
    const ADMIN_FORUM_SUBFORUMS_UPDATE              = '/admin/forum/subforum/update';
    const ADMIN_FORUM_SUBFORUMS_DELETE              = '/admin/forum/subforum/delete';
    const ADMIN_FORUM_TOPICS_MIGRATE                = '/admin/forum/topic/migrate';
    const ADMIN_FORUM_POSTS_MIGRATE                 = '/admin/forum/post/migrate';

    // admin/plugins
    const ADMIN_PLUGINS_MANAGEMENT                  = '/admin/plugins/default/list';
    const ADMIN_PLUGINS_MANAGEMENT_CONTROLLER       = '/admin/plugins/default';
    const ADMIN_PLUGINS_MANAGEMENT_ACTION           = '/admin/plugins/default/<action>';
    const ADMIN_PLUGINS_MANAGEMENT_GET              = '/admin/plugins/default/get';
    const ADMIN_PLUGINS_MANAGEMENT_ACTIVATE         = '/admin/plugins/default/activate';
    const ADMIN_PLUGINS_MANAGEMENT_UPDATE           = '/admin/plugins/default/update';
    const ADMIN_PLUGINS_MANAGEMENT_DEACTIVATE       = '/admin/plugins/default/deactivate';

    // admin/widgets
    const ADMIN_WIDGETS_MANAGEMENT                  = '/admin/widgets/default/list';
    const ADMIN_WIDGETS_MANAGEMENT_CONTROLLER       = '/admin/widgets/default';
    const ADMIN_WIDGETS_MANAGEMENT_ACTION           = '/admin/widgets/default/<action>';
    const ADMIN_WIDGETS_MANAGEMENT_GET              = '/admin/widgets/default/get';
    const ADMIN_WIDGETS_MANAGEMENT_ADD              = '/admin/widgets/default/add';
    const ADMIN_WIDGETS_MANAGEMENT_UPDATE           = '/admin/widgets/default/update';
    const ADMIN_WIDGETS_MANAGEMENT_DELETE           = '/admin/widgets/default/delete';

    //////////
    // site //
    //////////

    const SITE_HOME                                 = '/site/default/index';
    const SITE_ERROR                                = '/site/default/error';
    const SITE_CAPTCHA                              = '/site/default/captcha';

    // site/users
    const SITE_USERS_LOGIN                          = '/site/users/security/login';
    const SITE_USERS_AUTH                           = '/site/users/security/auth';
    const SITE_USERS_LOGOUT                         = '/site/users/security/logout';
    const SITE_USERS_REGISTRATION_REGISTER          = '/site/users/registration/register';
    const SITE_USERS_REGISTRATION_CONNECT           = '/site/users/registration/connect';
    const SITE_USERS_REGISTRATION_RESEND            = '/site/users/registration/resend';
    const SITE_USERS_REGISTRATION_CONFIRM           = '/site/users/registration/confirm';
    const SITE_USERS_PROFILE_SHOW                   = '/site/users/profile/show';
    const SITE_USERS_SETTINGS_PROFILE               = '/site/users/settings/profile';
    const SITE_USERS_SETTINGS_PROFILE_IMAGE_UPLOAD  = '/site/users/settings/profile-image-upload';
    const SITE_USERS_SETTINGS_ACCOUNT               = '/site/users/settings/account';
    const SITE_USERS_SETTINGS_NETWORKS              = '/site/users/settings/networks';
    const SITE_USERS_SETTINGS_CONFIRM               = '/site/users/settings/confirm';
    const SITE_USERS_RECOVERY_REQUEST               = '/site/users/recovery/request';
    const SITE_USERS_RECOVERY_RESET                 = '/site/users/recovery/reset';

    // site/pages
    const SITE_PAGES_SHOW                           = '/site/pages/default/show';

    // site/news
    const SITE_NEWS                                 = '/site/news/default/index';
    const SITE_NEWS_SHOW                            = '/site/news/default/show';
    const SITE_NEWS_CREATE                          = '/site/news/default/create';
    const SITE_NEWS_UPDATE                          = '/site/news/default/update';
    const SITE_NEWS_DELETE                          = '/site/news/default/delete';

    // site/forum
    const SITE_FORUM                                = '/site/forum/default/index';
    const SITE_FORUM_SECTIONS_SHOW                  = '/site/forum/section/show';
    const SITE_FORUM_SUBFORUMS_SHOW                 = '/site/forum/subforum/show';
    //const SITE_FORUM_TOPICS_ACTION                   = '/site/forum/topic/<action>';
    const SITE_FORUM_TOPICS_SHOW                    = '/site/forum/topic/show';
    const SITE_FORUM_TOPICS_CREATE                  = '/site/forum/topic/create';
    const SITE_FORUM_TOPICS_UPDATE                  = '/site/forum/topic/update';
    const SITE_FORUM_TOPICS_DELETE                  = '/site/forum/topic/delete';
    //const SITE_FORUM_POSTS_ACTION                    = '/site/forum/post/<action>';
    const SITE_FORUM_POSTS_CREATE                   = '/site/forum/post/create';
    const SITE_FORUM_POSTS_UPDATE                   = '/site/forum/post/update';
    const SITE_FORUM_POSTS_DELETE                   = '/site/forum/post/delete';

    // site/plugins
    const SITE_PLUGINS_SHOW                         = '/site/plugins/default/show';
    const SITE_PLUGINS_API_REQUEST                  = '/site/plugins/api/request';
}
