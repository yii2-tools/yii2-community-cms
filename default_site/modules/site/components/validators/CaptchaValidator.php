<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 25.01.16 18:58
 */

namespace site\components\validators;

use Yii;
use yii\captcha\CaptchaValidator as BaseCaptchaValidator;
use app\helpers\ModuleHelper;

class CaptchaValidator extends BaseCaptchaValidator
{
    protected function validateValue($value)
    {
        $captcha = &Yii::$app->getModule(ModuleHelper::SITE)->captcha;

        if (!$captcha->isExpired()) {
            return null;
        }

        $result = parent::validateValue($value);

        if (!$result) {
            $captcha->disableBy($this);
        }

        return $result;
    }
}
