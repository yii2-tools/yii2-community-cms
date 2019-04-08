<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 18.04.2016 03:50
 * via Gii Module Generator
 */

namespace site\modules\api;

use Yii;
use app\components\Module as BaseModule;

class Module extends BaseModule
{
    /**
     * @inheritdoc
     *
     * Specific prefix will be added to each $id.
     */
    public function getModule($id, $load = true)
    {
        if ('v' !== $id[0]) {
            $id = 'v' . $id;
        }

        return parent::getModule($id, $load);
    }
}
