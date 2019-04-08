<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 03.02.16 9:39
 */

namespace admin\modules\users;

use Yii;
use yii\base\BootstrapInterface;
use app\helpers\ModuleHelper;
use app\helpers\RouteHelper;
use app\modules\admin\components\Module as BaseModule;

/**
 * Class Module
 * @package admin\modules\users
 */
class Module extends BaseModule implements BootstrapInterface
{
    /** @var array An array of administrator's id. */
    public $admins = [];

    /**
     * @inheritdoc
     */
    public function actions()
    {
        $users = [
            'label' => Yii::t('app', 'Overview'),
            'description' => Yii::t(ModuleHelper::ADMIN_USERS, 'Users management'),
            'url' => [RouteHelper::ADMIN_USERS_MANAGEMENT],
            'right' => [
                'label' => $this->siteModule->params['users_count']
            ]
        ];

        if (in_array('/' . Yii::$app->requestedRoute, [
                RouteHelper::ADMIN_USERS_MANAGEMENT_UPDATE,
                RouteHelper::ADMIN_USERS_MANAGEMENT_UPDATE_PROFILE,
                RouteHelper::ADMIN_USERS_MANAGEMENT_INFO,
                RouteHelper::ADMIN_USERS_MANAGEMENT_ASSIGNMENTS,
        ])) {
            $users['active'] = true;
        }

        $groups = [
            'label' => Yii::t(ModuleHelper::USERS, 'Roles'),
            'description' => Yii::t(ModuleHelper::ADMIN_USERS, 'Roles management'),
            'url' => [RouteHelper::ADMIN_USERS_ROLES],
            'right' => [
                'label' => $this->siteModule->params['roles_count']
            ]
        ];

        if (strpos(RouteHelper::ADMIN_USERS_ROLES_UPDATE, Yii::$app->requestedRoute) !== false) {
            $groups['active'] = true;
        }

        return [
            $users,
            $groups,
            [
                'label' => Yii::t(ModuleHelper::USERS, 'Permissions'),
                'description' => Yii::t(ModuleHelper::ADMIN_USERS, 'Permissions overview'),
                'url' => [RouteHelper::ADMIN_USERS_PERMISSIONS],
                // active items only should be shown! fix required.
//                'right' => [
//                    'label' => $this->siteModule->params['permissions_count']
//                ],
            ],
            [
                'label' => Yii::t(ModuleHelper::ADMIN_USERS, 'Create'),
                'url' => false,
                'items' => [
                    [
                        'label' => Yii::t(ModuleHelper::USERS, 'User'),
                        'url' => [RouteHelper::ADMIN_USERS_MANAGEMENT_CREATE],
                        'icon' => 'plus',
                    ],
                    [
                        'label' => Yii::t(ModuleHelper::USERS, 'Role'),
                        'url' => [RouteHelper::ADMIN_USERS_ROLES_CREATE],
                        'icon' => 'plus',
                    ],
                    [
                        'label' => Yii::t(ModuleHelper::USERS, 'Permission'),
                        'url' => [RouteHelper::ADMIN_USERS_PERMISSIONS_CREATE],
                        'icon' => 'plus',
                        'visible' => YII_ENV_DEV,
                    ]
                ],
                'active' => Yii::$app->controller->module instanceof self
                    && Yii::$app->controller->action->id === 'create',
            ]
        ];
    }
}
