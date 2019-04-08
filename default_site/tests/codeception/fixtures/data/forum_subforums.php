<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 30.04.16 21:57
 */

return [
    [
        'id' => 1,
        'section_id' => 1,
        'title' => 'Subforum 1',
        'slug' => 'subforum-1',
        'description' => 'This is Subforum 1 description...',
        'topics_num' => 3,
        'posts_num' => 2,
        'last_post_id' => 5,
        'rbac_on' => 0,
        'rbac_item' => 'ACCESS_FORUM_SUBFORUMS_1',
        'position' => 0,
        'created_at' => time(),
        'updated_at' => time(),
    ],
    [
        'id' => 2,
        'section_id' => 1,
        'title' => 'Subforum 2',
        'slug' => 'subforum-2',
        'description' => 'This is Subforum 2 description...',
        'topics_num' => 0,
        'posts_num' => 0,
        'last_post_id' => 0,
        'rbac_on' => 0,
        'rbac_item' => 'ACCESS_FORUM_SUBFORUMS_2',
        'position' => 1,
        'created_at' => time(),
        'updated_at' => time(),
    ],
];
