<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 02.05.16 12:41
 */

return [
    [
        'id' => 1,
        'topic_id' => 1,
        'content' => 'This is test content of [is_first] <strong>Post #1</strong>',
        'is_first' => 1,
        'rbac_on' => 0,
        'rbac_item' => 'ACCESS_FORUM_POSTS_1',
        'created_by' => 1,
        'updated_by' => 0,
        'created_at' => time(),
        'updated_at' => time(),
    ],
    [
        'id' => 2,
        'topic_id' => 2,
        'content' => 'This is test content of [is_first] <strong>Post #2</strong>',
        'is_first' => 1,
        'rbac_on' => 0,
        'rbac_item' => 'ACCESS_FORUM_POSTS_2',
        'created_by' => 2,
        'updated_by' => 0,
        'created_at' => time(),
        'updated_at' => time(),
    ],
    [
        'id' => 3,
        'topic_id' => 2,
        'content' => 'This is test content of <strong>Post #3</strong>',
        'is_first' => 0,
        'rbac_on' => 0,
        'rbac_item' => 'ACCESS_FORUM_POSTS_3',
        'created_by' => 3,
        'updated_by' => 0,
        'created_at' => time(),
        'updated_at' => time(),
    ],
    [
        'id' => 4,
        'topic_id' => 3,
        'content' => 'This is test content of [is_first] <strong>Post #4</strong>',
        'is_first' => 1,
        'rbac_on' => 0,
        'rbac_item' => 'ACCESS_FORUM_POSTS_4',
        'created_by' => 4,
        'updated_by' => 0,
        'created_at' => time(),
        'updated_at' => time(),
    ],
    [
        'id' => 5,
        'topic_id' => 3,
        'content' => 'This is test content of <strong>Post #5</strong>',
        'is_first' => 0,
        'rbac_on' => 0,
        'rbac_item' => 'ACCESS_FORUM_POSTS_5',
        'created_by' => 1,
        'updated_by' => 0,
        'created_at' => time(),
        'updated_at' => time(),
    ],
];
