<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 28.01.16 8:51
 */

namespace app\helpers;

use Yii;

/**
 * All available app modules for current engine version.
 *
 * Class ModuleHelper
 * @package app\helpers
 * @since 2.0.0
 */
class ModuleHelper extends BaseHelper
{
    // engine
    const ROUTING       = 'routing';
    const MIGRATIONS    = 'migrations';
    const INTEGRATIONS  = 'integrations';
    const SERVICES      = 'services';
    const CORE_I18N     = 'ci18n';

    // site
    const SITE          = 'site';
    const I18N          = 'site/i18n';
    const USERS         = 'site/users';
    const DESIGN        = 'site/design';
    const PAGES         = 'site/pages';
    const FORUM         = 'site/forum';
    const API           = 'site/api';
    const PLUGINS       = 'site/plugins';
    const WIDGETS       = 'site/widgets';
    const NEWS          = 'site/news';

    // admin
    const ADMIN                 = 'admin';
    const ADMIN_SETUP           = 'admin/setup';
    const ADMIN_USERS           = 'admin/users';
    const ADMIN_DESIGN          = 'admin/design';
    const ADMIN_PAGES           = 'admin/pages';
    const ADMIN_NEWS            = 'admin/news';
    const ADMIN_FORUM           = 'admin/forum';
    const ADMIN_PLUGINS         = 'admin/plugins';
    const ADMIN_WIDGETS         = 'admin/widgets';

    /**
     * @param $uniqueId
     * @param $offset
     * @return mixed
     */
    public static function id($uniqueId, $offset = -1)
    {
        return Yii::$app->getFormatter()->asRoute($uniqueId, $offset);
    }
}
