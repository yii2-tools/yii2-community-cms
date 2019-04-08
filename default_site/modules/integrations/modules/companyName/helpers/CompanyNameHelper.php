<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 22.02.16 14:19
 */

namespace integrations\modules\companyName\helpers;

use app\modules\integrations\helpers\IntegrationHelper;

/**
 * Class CompanyNameHelper
 * @package integrations\modules\companyName\helpers
 */
class CompanyNameHelper extends IntegrationHelper
{
    // plugins
    const PLUGINS_GET           = 'plugins/get';
    const PLUGINS_ACTIVATE      = 'plugins/activate';
    const PLUGINS_UPDATE        = 'plugins/update';
    const PLUGINS_DEACTIVATE    = 'plugins/deactivate';

    // widgets
    const WIDGETS_GET           = 'widgets/get';
    const WIDGETS_ADD           = 'widgets/add';
    const WIDGETS_UPDATE        = 'widgets/update';
    const WIDGETS_DELETE        = 'widgets/delete';
}
