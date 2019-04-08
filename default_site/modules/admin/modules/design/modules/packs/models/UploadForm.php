<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 04.04.16 14:19
 */

namespace admin\modules\design\modules\packs\models;

use Yii;
use yii\helpers\FileHelper;
use yii\helpers\VarDumper;
use yii\base\Model;
use yii\web\UploadedFile;
use wapmorgan\UnifiedArchive\UnifiedArchive;
use app\helpers\ArchiveHelper;
use app\helpers\SecurityHelper;
use site\modules\design\helpers\ModuleHelper;
use design\modules\packs\helpers\DesignPackHelper;

// @todo refactor this class for php 5.5+ (add try-finally blocks instead of try-catch)
class UploadForm extends Model
{
    /**
     * @var \design\modules\packs\Module
     */
    public $module;

    /**
     * @var UploadedFile
     */
    public $file;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->module = Yii::$app->getModule(ModuleHelper::DESIGN_PACKS);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                ['file'], 'file',
                'skipOnEmpty' => false,
                'extensions' => $this->module->params['design_packs_format'],
                'checkExtensionByMimeType' => false,
                'mimeTypes' => $this->module->params['design_packs_mime_types'],
                'minSize' => 1,
                'maxSize' => $this->module->params['design_packs_size_max'],
                'wrongMimeType' => Yii::t(
                    'errors',
                    "File '{file}' has invalid MIME type. Valid formats: {extensions}",
                    ['extensions' => implode(', ', $this->module->params['design_packs_format'])]
                ),
            ],
        ];
    }

    /**
     * Upload action skeleton.
     *
     * @return bool
     */
    public function upload()
    {
        $this->file = UploadedFile::getInstance($this, 'file');
        Yii::trace('Uploading file' . PHP_EOL . VarDumper::dumpAsString($this->file), __METHOD__);

        if (!$this->validate()) {
            Yii::warning('Upload form validation failed: ' . VarDumper::dumpAsString($this->getErrors()), __METHOD__);

            return false;
        }

        try {
            return $this->uploadInternal();
        } catch (\Exception $e) {
            Yii::error($e, __METHOD__);

            $errors = $this->getErrors('file');
            if (empty($errors)) {
                $this->addError('file', Yii::t('errors', 'Engine error'));
            }

            return false;
        }
    }

    /**
     * Save and extract content of archive.
     *
     * @return bool
     * @throws \Exception
     */
    protected function uploadInternal()
    {
        $filepath = $this->save();

        try {
            $result = $this->extract($filepath);
            unlink($filepath);

            return $result;
        } catch (\Exception $e) {
            unlink($filepath);

            throw $e;
        }
    }

    /**
     * Creating new design pack from uploaded archive source
     *
     * @param string $filepath
     * @return bool|void
     * @throws \Exception
     */
    protected function extract($filepath)
    {
        $archive = ArchiveHelper::open($filepath);
        Yii::trace('Extracting archive' . PHP_EOL . VarDumper::dumpAsString($archive), __METHOD__);

        if (!$this->validateArchive($archive)) {
            return false;
        }

        list($sourceDir, $isRoot) = $this->extractInternal($filepath);
        $clearCallback = function () use ($sourceDir, $isRoot) {
            if (!$isRoot) {
                $sourceDir = dirname($sourceDir);
            }
            FileHelper::removeDirectory($sourceDir);
        };

        try {
            $result = $this->createDesignPack($sourceDir);
            $clearCallback();

            return $result;
        } catch (\Exception $e) {
            $clearCallback();

            throw $e;
        }
    }

    /**
     * Extracting archive files to source directory.
     *
     * This method additionally ensures that source directory is valid archive content directory.
     * For example:
     *
     * archive_name.zip
     *          |
     *           – archive_name
     *                      |
     *                       – file
     *
     * Should be transformed to:
     *
     * archive_name.zip
     *          |
     *           – file
     *
     * @param string $filepath
     * @return array $sourceDir first parameter, isRoot second
     */
    protected function extractInternal($filepath)
    {
        $sourceDir = Yii::getAlias('@design_packs_tmp/uploadedSource');
        ArchiveHelper::extract($filepath, $sourceDir);

        // Ensure root directory is valid.
        $filename = explode('.', pathinfo($filepath, PATHINFO_FILENAME))[0];
        $realSourceDir = $sourceDir . DIRECTORY_SEPARATOR . $filename;
        if (is_dir($realSourceDir)) {
            return [$realSourceDir, false];
        }

        return [$sourceDir, true];
    }

    /**
     * Performs final stage of design pack uploading: copying files and creates db record
     * Delegates all logic within design pack's context to relevant helpers
     *
     * @param string $sourceDir path to design pack's files
     * @return bool
     * @throws \Exception
     */
    protected function createDesignPack($sourceDir)
    {
        Yii::trace('Creating design pack from source: ' . $sourceDir, __METHOD__);

        DesignPackHelper::validate($sourceDir, $error);
        if (!empty($error)) {
            $this->addError('file', $error);

            return false;
        }

        if (!DesignPackHelper::create($sourceDir, $error)) {
            if (!empty($error)) {
                $this->addError('file', $error);
            }

            throw new \Exception('Design pack creation failed (internal)');
        }

        return true;
    }

    /**
     * Performs archive saving and returns path to his location in temp directory
     *
     * @return string
     * @throws \Exception
     */
    protected function save()
    {
        $dir = Yii::getAlias('@design_packs_tmp');
        FileHelper::createDirectory($dir);
        $filepath = $dir . DIRECTORY_SEPARATOR . $this->file->name;

        Yii::trace('Saving file: ' . $filepath, __METHOD__);

        if (!$this->file->saveAs($filepath)) {
            throw new \Exception("UploadedFile saveAs failed");
        }

        return $filepath;
    }

    /**
     * Performs pre-checks content of archive before extracting action.
     *
     * @param UnifiedArchive $archive
     * @return bool
     */
    protected function validateArchive(UnifiedArchive $archive)
    {
        Yii::trace('Archive validation', __METHOD__);

        $fileNames = $archive->getFileNames();

        if (!$this->validateFileNames($fileNames)) {
            return false;
        }

        return true;
    }

    /**
     * Validates array of file names in special context of security
     *
     * @param array $fileNames
     * @return bool
     */
    protected function validateFileNames($fileNames)
    {
        Yii::trace('Archive files validation' . PHP_EOL . VarDumper::dumpAsString($fileNames), __METHOD__);

        foreach ($fileNames as $fileName) {
            $error = null;
            SecurityHelper::validateFilePath($fileName, $error);

            if (!empty($error)) {
                $this->addError('file', $error);
                Yii::warning("Security validation failed for filepath '$fileName':" . PHP_EOL . $error, __METHOD__);

                return false;
            }
        }

        return true;
    }
}
