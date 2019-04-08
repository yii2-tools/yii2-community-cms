<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 16.04.2016 14:50
 * via Gii Module Generator
 */

namespace app\modules\services;

use Yii;
use yii\base\BootstrapInterface;
use app\components\Module as BaseModule;

class Module extends BaseModule implements BootstrapInterface
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        Yii::$container->setSingleton('app\modules\services\interfaces\ManagerInterface', $this->getManager());
    }

    /**
     * @return \app\modules\services\interfaces\ManagerInterface
     */
    public function getManager()
    {
        return $this->get('manager');
    }
}
