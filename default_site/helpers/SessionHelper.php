<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 25.01.16 3:13
 */

namespace app\helpers;

/**
 * Class SessionHelper
 *
 * @package app\helpers
 * @since   2.0.0
 */
class SessionHelper
{
    // site
    const AJAX_REDIRECT    = 'site_ajax_redirect';
    const CAPTCHA_REQUIRED = 'site_captcha_required';
    const CAPTCHA_EXPIRE   = 'site_captcha_expire_at';

    // users
    const AUTH_ATTEMPTS = 'site_users_auth_attempts';
    const AUTH_LOGIN    = 'site_users_auth_login';
}
