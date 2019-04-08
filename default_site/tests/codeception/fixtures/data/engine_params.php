<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 20.03.16 18:14
 */

use app\helpers\ModuleHelper as M;

return [
    ['copyright', 'Copyright © {Team name}, {сity}', 'site/design', time(), time()],
    ['default_role', Yii::t(M::ADMIN_USERS, 'User'), 'site/users', time(), time()],
    ['disk_space', 10485760, 'site', time(), time()],
    ['forum_active', 1, 'site/forum', time(), time()],
    ['forum_posts_count', 0, 'site/forum', time(), time()],
    ['forum_topics_count', 5, 'site/forum', time(), time()],
    ['guest_role', Yii::t(M::ADMIN_USERS, 'Guest'), 'site/users', time(), time()],
    ['last_user', 'admin', 'site/users', time(), time()],
    ['title', 'Site of the team', 'site/design', time(), time()],
    ['users_count', 4, 'site/users', time(), time()],
    ['roles_count', 8, 'site/users', time(), time()],
    ['permissions_count', 53, 'site/users', time(), time()],
    ['news_count', 1, 'site/news', time(), time()],
];
