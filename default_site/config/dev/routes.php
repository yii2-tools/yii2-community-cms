<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 08.02.16 15:43
 */

use app\helpers\RouteHelper;

return [
    'debug'                                         => RouteHelper::DEBUG_HOME,
    'debug/<controller:[\w\-]+>/<action:[\w\-]+>'   => RouteHelper::DEBUG_CONTROLLER_ACTION,
    'gii'                                           => RouteHelper::GII_HOME,
    'gii/<id:\w+>'                                  => RouteHelper::GII_VIEW,
    'gii/<controller:[\w\-]+>/<action:[\w\-]+>'     => RouteHelper::GII_CONTROLLER_ACTION,
];