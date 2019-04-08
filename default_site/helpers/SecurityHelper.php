<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 11.03.16 18:23
 */

namespace app\helpers;

use Yii;
use app\components\validators\FileValidator;

/**
 * Check entities for suspicious data holding and behaviors
 *
 * Class SecurityHelper
 * @package app\helpers
 * @since 2.0.0
 */
class SecurityHelper
{
    /**
     * Checks filepath for suspicious parts.
     *
     * @param string $filepath
     * @param $error
     * @return bool validation result
     * @since 2.0.0
     */
    public static function validateFilePath($filepath, &$error = null)
    {
        return FileValidator::validateFilePath($filepath, $error);
    }

    /**
     * This method works similar to yii\validators\FileValidator
     * except it's standalone static function and it's consumes row file/dir path
     *
     * @param string $filepath path to file/dir
     * @param array $config configuration array for FileValidator instance
     * @param $error
     * @return bool validation result
     * @see app\components\validators\FileValidator
     */
    public static function validateFile($filepath, array $config, &$error = null)
    {
        /** @var FileValidator $fileValidator */
        $fileValidator = Yii::$container->get(FileValidator::className(), [], $config);

        return $fileValidator->validateByPath($filepath, $error);
    }
}
