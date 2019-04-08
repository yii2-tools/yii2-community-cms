<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 18.04.16 4:09
 */

namespace site\modules\api\components;

use Yii;
use app\components\Module as BaseModule;

class Module extends BaseModule
{
    /**
     * @inheritdoc
     *
     * Returns API component with $name (if exists)
     */
    public function __call($name, $arguments)
    {
        if ($component = $this->get($name, false)) {
            return $component;
        }

        Yii::error("API component '$name' doesn't exists for version '{$this->params['version']}'", __METHOD__);

        return parent::__call($name, $arguments);
    }
}
