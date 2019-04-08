<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 01.05.16 10:45
 */

return [
    // fixed, open topic
    [
        'id' => 1,
        'subforum_id' => 1,
        'title' => 'Fixed Open Topic 1',
        'slug' => 'fixed-open-topic-1',
        'description' => 'The Fixed Open Topic 1 description...',
        'views_num' => '3',
        'posts_num' => 0,
        'is_closed' => 0,
        'is_fixed' => 1,
        'last_post_id' => 0,
        'rbac_on' => 0,
        'rbac_item' => 'ACCESS_FORUM_TOPICS_1',
        'created_by' => 1,
        'updated_by' => 0,
        'created_at' => time(),
        'updated_at' => time(),
    ],
    // non-fixed, open topic
    [
        'id' => 2,
        'subforum_id' => 1,
        'title' => 'Open Topic 2',
        'slug' => 'open-topic-2',
        'description' => 'The Open Topic 2 description...',
        'views_num' => '14',
        'posts_num' => 1,
        'is_closed' => 0,
        'is_fixed' => 0,
        'last_post_id' => 1,
        'rbac_on' => 0,
        'rbac_item' => 'ACCESS_FORUM_TOPICS_2',
        'created_by' => 2,
        'updated_by' => 0,
        'created_at' => time(),
        'updated_at' => time(),
    ],
    // non-fixed, closed topic
    [
        'id' => 3,
        'subforum_id' => 1,
        'title' => 'Closed Topic 3',
        'slug' => 'closed-topic-3',
        'description' => 'The Closed Topic 3 description...',
        'views_num' => '21',
        'posts_num' => 1,
        'is_closed' => 1,
        'is_fixed' => 0,
        'last_post_id' => 5,
        'rbac_on' => 0,
        'rbac_item' => 'ACCESS_FORUM_TOPICS_3',
        'created_by' => 4,
        'updated_by' => 0,
        'created_at' => time(),
        'updated_at' => time(),
    ],
];
