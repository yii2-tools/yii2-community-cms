<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 23.01.16 16:04
 */

use app\helpers\ModuleHelper;
use yii\tools\params\models\ActiveParam;

return [
    'name' => Yii::t(ModuleHelper::USERS, 'Users'),
    'version' => '2.0.1',
    'last_user' => [
        'class' => 'yii\tools\params\String',
        'flags' => ActiveParam::READ_ONLY,
        'description' => Yii::t(ModuleHelper::USERS, 'Last registered user'),
    ],
    'default_role' => [
        'class' => 'site\modules\users\models\RoleParam',
        'description' => Yii::t(ModuleHelper::USERS, 'Default role for new users'),
    ],
    'guest_role' => [
        'class' => 'site\modules\users\models\RoleParam',
        'description' => Yii::t(ModuleHelper::USERS, 'Role for guests'),
    ],
    'users_count' => [
        'class' => 'yii\tools\params\Number',
        'flags' => ActiveParam::READ_ONLY,
        'description' => Yii::t(ModuleHelper::USERS, 'Users count'),
    ],
    'roles_count' => [
        'class' => 'yii\tools\params\Number',
        'flags' => ActiveParam::READ_ONLY,
        'description' => Yii::t(ModuleHelper::USERS, 'Roles count'),
    ],
    'permissions_count' => [
        'class' => 'yii\tools\params\Number',
        'flags' => ActiveParam::READ_ONLY,
        'description' => Yii::t(ModuleHelper::USERS, 'Permissions count'),
    ],
];
