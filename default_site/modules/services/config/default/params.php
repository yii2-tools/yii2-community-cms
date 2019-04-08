<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 16.04.2016 14:50
 * via Gii Module Generator
 */

use app\modules\services\helpers\ServiceHelper;

return [
    'name' => 'Services',
    'version' => '2.0.0',
    'services' => [
        ServiceHelper::PACK_BASE => 'Base service pack',
        ServiceHelper::ANALYTICS => 'Analytics system',
    ],
];