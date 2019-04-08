<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 04.04.16 20:22
 */

namespace design\modules\packs\helpers;

use Yii;
use yii\helpers\HtmlPurifier;
use yii\helpers\VarDumper;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\helpers\Json;
use app\helpers\VersionHelper;
use app\helpers\SecurityHelper;
use site\modules\design\helpers\ModuleHelper;
use design\modules\packs\models\DesignPack;
use design\modules\packs\Finder;

/**
 * Class DesignPackHelper
 * @package design\modules\packs\helpers
 * @since 2.0.0
 */
class DesignPackHelper
{
    private static $localCache = [];

    /**
     * Creates new DesignPack object based on attributes (without saving to the database)
     *
     * @param array $attributes
     * @return DesignPack
     */
    public static function build(array $attributes)
    {
        $model = Yii::createObject(DesignPack::className());
        $model->setAttributes($attributes);

        return $model;
    }

    /**
     * Validates $sourceDir in context of design pack's content.
     *
     * Note: this method don't perform config checks,
     * for actual config checks use [[validateConfig]] instead (pre-parse required).
     *
     * @param string $sourceDir path to design pack's files
     * @param $error
     * @return bool
     */
    public static function validate($sourceDir, &$error = null)
    {
        $module = Yii::$app->getModule(ModuleHelper::DESIGN_PACKS);
        $filterCallback = $module->getPathFilter()->buildFilterCallback();
        $files = FileHelper::findFiles($sourceDir, ['filter' => $filterCallback]);
        $config = [
            'extensions' => $module->params['design_packs_content_format'],
            'checkExtensionByMimeType' => false,
            'mimeTypes' => $module->params['design_packs_content_mime_types'],
            'minSize' => 1,
            'maxSize' => $module->params['design_packs_content_size_max'],
            'wrongExtension' => Yii::t(
                'errors',
                "File '{file}' has invalid extension. Valid extensions: {extensions}",
                ['extensions' => implode(', ', $module->params['design_packs_content_format'])]
            ),
            'wrongMimeType' => Yii::t(
                'errors',
                "File '{file}' has invalid MIME type. Valid formats: {extensions}",
                ['extensions' => implode(', ', $module->params['design_packs_content_format'])]
            ),
        ];

        foreach ($files as $filepath) {
            SecurityHelper::validateFile($filepath, $config, $error);

            if (!empty($error)) {
                $dump = array_merge(pathinfo($filepath), ['mimetype' => FileHelper::getMimeType($filepath)]);
                Yii::warning("Validation for file '$filepath' failed"
                    . PHP_EOL . VarDumper::dumpAsString($dump), __METHOD__);

                return false;
            }
        }

        return true;
    }

    /**
     * Performs design pack's config validation.
     *
     * @param array|string $config parsed design pack's config file as PHP array,
     * if string, it will be parsed into PHP array (string treated as $sourceDir)
     * @param $error
     * @return bool
     */
    public static function validateConfig($config, &$error = null)
    {
        if (!is_array($config)) {
            $config = static::parseConfig($config);
        }

        // Ensure required params.
        $requiredParams = [
            DesignPack::CONFIG_PARAM_NAME,
            DesignPack::CONFIG_PARAM_TITLE,
            DesignPack::CONFIG_PARAM_VERSION
        ];

        foreach ($requiredParams as $name) {
            if (empty($config[$name])) {
                $error = Yii::t('errors', "Config param '{0}' must be defined", $name);

                return false;
            }
        }

        $module = Yii::$app->getModule(ModuleHelper::DESIGN_PACKS);

        // Ensure that name doesn't exists in reserved names and has valid format.
        $configName = $config[DesignPack::CONFIG_PARAM_NAME];
        $reservedNames = $module->params['design_packs_name_reserved'];
        if (in_array($configName, $reservedNames)) {
            $error = Yii::t(
                'errors',
                "Value '{0}' of config param '{1}' is restricted",
                [$configName, DesignPack::CONFIG_PARAM_NAME]
            );

            return false;
        }
        $namePattern = $module->params['design_packs_name_pattern'];
        if (!preg_match($namePattern, $configName)) {
            $error = Yii::t(
                'errors',
                "Value '{0}' of config param '{1}' has invalid format",
                [$configName, DesignPack::CONFIG_PARAM_NAME]
            );

            return false;
        }

        // @todo preview image checks

        $configVersion = $config[DesignPack::CONFIG_PARAM_VERSION];
        if (!preg_match(VersionHelper::PATTERN, $configVersion)) {
            $error = Yii::t(
                'errors',
                "Value '{0}' of config param '{1}' has invalid format",
                [$configVersion, DesignPack::CONFIG_PARAM_VERSION]
            );
            return false;
        }

        return true;
    }

    /**
     * Validates resource as preview for design pack.
     *
     * @param string $filepath path to preview resource
     * @return bool
     */
    public static function validatePreview($filepath)
    {
        // @todo

        return true;
    }

    /**
     * Performs design pack's config file parsing into PHP array.
     *
     * @param string $sourceDir path to design pack's files
     * @param $error
     * @return array|false
     */
    public static function parseConfig($sourceDir, &$error = null)
    {
        if (isset(static::$localCache[$sourceDir])) {
            return static::$localCache[$sourceDir];
        }

        $name = DesignPack::CONFIG_FILE_NAME;
        $filepath = Yii::getAlias($sourceDir . DIRECTORY_SEPARATOR . $name);
        try {
            $config = ArrayHelper::htmlEncode(Json::decode(HtmlPurifier::process(file_get_contents($filepath))));
            Yii::info("Design pack's config parsed" . PHP_EOL . VarDumper::dumpAsString($config), __METHOD__);

            return static::$localCache[$sourceDir] = $config;
        } catch (\Exception $e) {
            Yii::error("An error occurred during parsing config file '$filepath'"
                . PHP_EOL . VarDumper::dumpAsString($e), __METHOD__);
            $error = Yii::t('errors', 'Invalid config file "{0}"', $name);

            return false;
        }
    }

    /**
     * Creates new design pack based on $sourceDir content and register it in database.
     * $sourceDir should contain design pack's config file.
     *
     * @param string $sourceDir path to design pack's files
     * @param $error
     * @return DesignPack|null
     */
    public static function create($sourceDir, &$error = null)
    {
        $sourceDir = Yii::getAlias($sourceDir);
        $transaction = DesignPack::getDb()->beginTransaction();
        try {
            if ($config = static::parseConfig($sourceDir, $error)) {
                static::validateConfig($config, $error);
            }
            if (!empty($error)) {
                return null;
            }

            $designPack = static::createInternal($sourceDir, $config, $error);
            if (!empty($error)) {
                return null;
            }

            $transaction->commit();

            return $designPack;
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error('An error occurred during design pack creation'
                . PHP_EOL . VarDumper::dumpAsString($e), __METHOD__);

            return null;
        }
    }

    /**
     * Actual design pack creation logic.
     *
     * @param string $sourceDir
     * @param array $config
     * @param $error
     * @return DesignPack|null
     * @throws \Exception
     */
    protected static function createInternal($sourceDir, array $config, &$error = null)
    {
        $module = Yii::$app->getModule(ModuleHelper::DESIGN_PACKS);
        $limit = $module->params['design_packs_limit'];
        $currentCount = $module->module->params['design_packs_count'];
        Yii::info("Design packs count: " . $currentCount . ", limit: " . $limit, __METHOD__);

        if ($currentCount >= $limit) {
            Yii::warning("Maximum number of available design packs exceeded"
                . ' (' . $currentCount . '/' . $limit . ')', __METHOD__);
            $error = Yii::t(
                ModuleHelper::ADMIN_DESIGN,
                'Maximum number of available design packs exceeded ({0}/{0})',
                $limit
            );

            return null;
        }

        $model = static::createModelByConfig($config);

        if (!$model->isCompatible()) {
            $error = Yii::t(
                ModuleHelper::ADMIN_DESIGN,
                "Design pack '{0}' is not compatible with current engine version '{1}'",
                $config[DesignPack::CONFIG_PARAM_NAME],
                Yii::$app->version
            );

            return null;
        }

        if (!$model->save()) {
            throw new \LogicException('Design pack save() failed'
                . PHP_EOL . VarDumper::dumpAsString($model->getErrors()));
        }

        static::createRelatedFiles($sourceDir, $config);
        FileHelper::createDirectory($model->getTmpDir());

        return $model;
    }

    /**
     * @param array $config
     * @return DesignPack
     */
    protected static function createModelByConfig(array $config)
    {
        $name = ArrayHelper::getValue($config, DesignPack::CONFIG_PARAM_NAME);
        $attributes = [
            'name' => $name,
            'title' => ArrayHelper::getValue($config, DesignPack::CONFIG_PARAM_TITLE),
            'description' => ArrayHelper::getValue($config, DesignPack::CONFIG_PARAM_DESCRIPTION),
            'preview' => ArrayHelper::getValue($config, DesignPack::CONFIG_PARAM_PREVIEW),
            'version' => ArrayHelper::getValue($config, DesignPack::CONFIG_PARAM_VERSION),
        ];

        if ($model = Yii::createObject(Finder::className())->findDesignPack(['=', 'name', $name])) {
            $model->setAttributes($attributes);
            $model->setScenario(DesignPack::SCENARIO_UPDATE);
        } else {
            $model = static::build($attributes);
            $model->setScenario(DesignPack::SCENARIO_CREATE);
        }

        return $model;
    }

    /**
     * @param string $sourceDir
     * @param array $config
     * @throws \Exception
     */
    protected static function createRelatedFiles($sourceDir, array $config)
    {
        $destDir = Yii::getAlias('@design_packs_dir' . DIRECTORY_SEPARATOR . $config[DesignPack::CONFIG_PARAM_NAME]);
        $filterCallback = Yii::$app->getModule(ModuleHelper::DESIGN_PACKS)->getPathFilter()->buildFilterCallback();

        try {
            FileHelper::copyDirectory($sourceDir, $destDir, ['filter' => $filterCallback, 'fileMode' => 0775]);
        } catch (\Exception $e) {
            FileHelper::removeDirectory($destDir);

            throw $e;
        }
    }
}
