<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 23.03.16 17:46
 */

namespace site\modules\design\helpers;

use Yii;
use app\helpers\ModuleHelper as BaseModuleHelper;

/**
 * All available submodules
 *
 * Class ModuleHelper
 * @package design\modules\content\helpers
 * @since 2.0.0
 */
class ModuleHelper extends BaseModuleHelper
{
    const DESIGN_CONTENT    = 'site/design/content';
    const DESIGN_PACKS      = 'site/design/packs';
    const DESIGN_MENU       = 'site/design/menu';
}
