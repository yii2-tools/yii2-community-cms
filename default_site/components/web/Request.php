<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 25.01.16 21:07
 */

namespace app\components\web;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\Request as BaseRequest;

class Request extends BaseRequest
{
    private $cacheRoute = null;

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function resolve($routeStringOnly = false)
    {
        if (!$this->cacheRoute) {
            $rules = ArrayHelper::map(Yii::$app->getUrlManager()->rules, 'name', 'route');
            Yii::info('URL rules for current request' . PHP_EOL . VarDumper::dumpAsString($rules), __METHOD__);

            $this->cacheRoute = parent::resolve();
        }

        return $routeStringOnly ? $this->cacheRoute[0] : $this->cacheRoute;
    }
}
