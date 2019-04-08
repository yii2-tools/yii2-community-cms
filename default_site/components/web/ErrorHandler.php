<?php

/**
 * Engine top-level error handler
 *
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 24.01.16 4:07
 */

namespace app\components\web;

use Yii;
use yii\web\ErrorHandler as BaseErrorHandler;

class ErrorHandler extends BaseErrorHandler
{
    /** @var string */
    public $globalErrorPattern = '/^(?!4)\d+/';

    /** @var array */
    public $globalErrorCodes = [];

    /**
     * @inheritdoc
     */
    public function handleException($exception)
    {
        $code = $exception instanceof \yii\web\HttpException
            ? $exception->statusCode
            : $exception->getCode();

        if (in_array($code, $this->globalErrorCodes) || preg_match($this->globalErrorPattern, $code)) {
            $this->reset();
        }

        parent::handleException($exception);
    }

    /**
     * @inheritdoc
     */
    public function handleFatalError()
    {
        $this->reset();
        parent::handleFatalError();
    }

    /**
     * Resets application params to default state (disable core module global changes)
     */
    protected function reset()
    {
        if (Yii::$app instanceof \yii\web\Application && Yii::$app->has('loader', true)) {
            Yii::$app->loader->reset();
        }
    }
}
