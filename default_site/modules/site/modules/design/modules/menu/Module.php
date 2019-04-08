<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 15.04.16 4:50
 */

namespace design\modules\menu;

use Yii;
use yii\base\BootstrapInterface;
use app\modules\site\components\Module as BaseModule;
use design\modules\menu\interfaces\ManagerInterface;

class Module extends BaseModule implements BootstrapInterface
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        Yii::$container->setSingleton('design\modules\menu\interfaces\ManagerInterface', $this->getManager());
    }

    /**
     * @return ManagerInterface
     */
    public function getManager()
    {
        return $this->get('manager');
    }
}
