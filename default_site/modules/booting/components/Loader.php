<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 24.01.16 5:14
 */

namespace app\modules\booting\components;

use Yii;
use yii\base\Component;
use yii\base\InvalidParamException;

class Loader extends Component
{
    /**
     * Note: this method used by admin module for loading site/<...> submodules
     * @param $module
     * @throws \yii\base\InvalidParamException
     */
    public function bootstrap($module)
    {
        if (!is_object($module) || !$module instanceof \yii\base\Module) {
            throw new InvalidParamException('$module must be instance of \yii\base\Module');
        }

        $module->bootstrap(Yii::$app);
    }

    /**
     * Resetting default app params
     * Usage example: uncaught errors, means that default app template needs to be used
     */
    public function reset()
    {
        Yii::$app->viewPath = Yii::$app->params['default_view_path'];
        Yii::$app->set('view', Yii::$app->params['default_class_view']);
        Yii::$app->getErrorHandler()->errorAction = Yii::$app->params['default_error_action'];
    }
}
