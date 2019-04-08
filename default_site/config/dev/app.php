<?php

/**
 * Low-level init operations (php-only environment)
 *
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 29.01.16 8:29
 */

error_reporting(E_ALL);
ini_set('display_errors', 'On');
ini_set('display_startup_errors', 'On');

// MAMP old timezonedb fix.
date_default_timezone_set('Etc/GMT-3');

defined('YII_DEBUG') or define('YII_DEBUG', true);
