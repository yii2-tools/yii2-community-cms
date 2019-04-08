<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 21.02.2016 06:07
 * via Gii Module Generator
 */

namespace integrations\modules\companyName;

use app\modules\integrations\helpers\IntegrationHelper;
use app\modules\integrations\components\IntegrationModuleAbstract;

/**
 * Class Module
 * @package integrations\modules\companyName
 */
class Module extends IntegrationModuleAbstract
{
    /** @inheritdoc */
    public $vendor = IntegrationHelper::COMPANY_NAME;
}
