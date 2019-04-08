<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 03.05.16 14:27
 */

namespace admin\modules\users\rbac;

use yii\rbac\Rule;

/**
 * Checks if created_by matches user passed via params
 */
class AuthorRule extends Rule
{
    public $name = 'IS_AUTHOR';

    /**
     * @inheritdoc
     */
    public function execute($user, $item, $params)
    {
        return isset($params['entity']) ? $params['entity']->created_by == $user : false;
    }
}
