<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 18.05.16 8:41
 */

namespace app\modules\admin\components;

use Yii;
use app\helpers\ModuleHelper;
use app\components\controllers\WebController;

/**
 * Class Controller
 * @package app\modules\admin\components
 */
class Controller extends WebController
{
    /**
     * @return null|string
     */
    public function globalTitle()
    {
        return Yii::$app->getModule(ModuleHelper::DESIGN)->params['title'];
    }
}
