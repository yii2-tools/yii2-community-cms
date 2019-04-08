<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 11.03.16 18:40
 */

namespace app\components\validators;

use Yii;
use yii\helpers\FileHelper;
use yii\validators\FileValidator as BaseFileValidator;

/**
 * Standalone FileValidator for files represented by row string path
 * @inheritdoc
 */
class FileValidator extends BaseFileValidator
{
    /**
     * Validates given filepath in context of security.
     *
     * @param string $value
     * @param string $error
     * @return bool
     */
    public static function validateFilePath($value, &$error = null)
    {
        if (preg_match('/\.\//', $value)) {
            $error = Yii::t('errors', 'Invalid file path "{0}"', $value);
            return false;
        }

        return true;
    }

    /**
     * Validates a file by given filepath.
     * You may use this method to validate a file out of the context of a UploadedFile model.
     *
     * @param mixed $value filepath to be validated.
     * @param string $error the error message to be returned, if the validation fails.
     * @return boolean whether the filepath and file is valid.
     */
    public function validateByPath($value, &$error = null)
    {
        $result = null;
        $filesize = filesize($value);
        $pathinfo = pathinfo($value);

        if ($this->maxSize !== null && $filesize > $this->getSizeLimit()) {
            $result = [
                $this->tooBig,
                [
                    'file' => $pathinfo['basename'],
                    'limit' => $this->getSizeLimit(),
                    'formattedLimit' => Yii::$app->formatter->asShortSize($this->getSizeLimit())
                ]
            ];
        } elseif ($this->minSize !== null && $filesize < $this->minSize) {
            $result = [
                $this->tooSmall,
                [
                    'file' => $pathinfo['basename'],
                    'limit' => $this->minSize,
                    'formattedLimit' => Yii::$app->formatter->asShortSize($this->minSize)
                ]
            ];
        } elseif (!empty($this->extensions) && !in_array($pathinfo['extension'], $this->extensions, true)) {
            $result = [
                $this->wrongExtension,
                [
                    'file' => $pathinfo['basename'],
                    'extensions' => implode(', ', $this->extensions)
                ]
            ];
        } elseif (!empty($this->mimeTypes) && !in_array(FileHelper::getMimeType($value), $this->mimeTypes, false)) {
            $result = [
                $this->wrongMimeType,
                [
                    'file' => $pathinfo['basename'],
                    'mimeTypes' => implode(', ', $this->mimeTypes)
                ]
            ];
        }

        if (empty($result)) {
            return true;
        }

        list($message, $params) = $result;
        $params['attribute'] = Yii::t('yii', 'the input value');
        $params['value'] = is_array($value) ? 'array()' : $value;
        $error = Yii::$app->getI18n()->format($message, $params, Yii::$app->language);

        return false;
    }
}
