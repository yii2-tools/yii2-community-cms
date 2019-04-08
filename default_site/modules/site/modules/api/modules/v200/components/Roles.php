<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 18.04.16 6:11
 */

namespace api\modules\v200\components;

use Yii;
use yii\base\Component;

/**
 * API component 'roles'
 *
 * Port from API v3 (engine 1.0)
 * @see <gitlab link>
 *
 * @package api\modules\v200\components
 */
class Roles extends Component
{
    /**
     * @param string $role_id auth item name
     * @return array|bool
     */
    public function getById($role_id)
    {
        if (!($item = Yii::$app->getAuthManager()->getRole($role_id))) {
            return false;
        }

        return [
            'role_id' => $item->name,
            'title' => $item->name,
        ];
    }
}
