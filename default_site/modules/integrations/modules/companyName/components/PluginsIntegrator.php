<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 21.02.16 6:57
 */

namespace integrations\modules\companyName\components;

use Yii;

/**
 * Class PluginsIntegrator
 * @package integrations\modules\companyName\components
 */
class PluginsIntegrator extends Integrator
{
    const MAIN_SERVER_QUERY_ACTION = 11;

    /** @inheritdoc */
    public $dataKey = 'plugin_key';

    /** @inheritdoc */
    public $dataKeyShort = 'pk';
}
