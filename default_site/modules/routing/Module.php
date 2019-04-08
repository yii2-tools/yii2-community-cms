<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 25.01.16 22:18
 */

namespace app\modules\routing;

use Yii;
use yii\base\BootstrapInterface;
use yii\base\Module as BaseModule;

class Module extends BaseModule implements BootstrapInterface
{
    public function bootstrap($app)
    {
        Yii::beginProfile('Stage: engine \'' . $this->id . '\'', __METHOD__);

        Yii::$app->set('router', [
            'class' => 'app\modules\routing\components\Router',
        ]);

        Yii::$app->getUrlManager()->addRules(Yii::$app->router->getUrlRules());

        Yii::endProfile('Stage: engine \'' . $this->id . '\'', __METHOD__);
    }
}
