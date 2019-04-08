<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 18.03.2016 20:13
 * via Gii Module Generator
 */

namespace i18n\modules\packs;

use Yii;
use yii\base\BootstrapInterface;
use app\modules\site\components\Module as BaseModule;

class Module extends BaseModule implements BootstrapInterface
{
    public function bootstrap($app)
    {
        parent::bootstrap($app);

        // All translations have been initialized in Core i18n (ci18n) module
    }
}
