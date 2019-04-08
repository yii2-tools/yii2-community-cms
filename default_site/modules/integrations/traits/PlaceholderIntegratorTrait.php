<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 25.03.16 18:59
 */

namespace app\modules\integrations\traits;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\NotSupportedException;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use design\modules\content\helpers\PlaceholderHelper;
use design\modules\content\models\ActivePlaceholder;
use design\modules\content\models\WidgetPlaceholder;

trait PlaceholderIntegratorTrait
{
    /** @var array */
    private $placeholderConfig;

    /**
     * @param int $type
     * @param string $integratedName
     * @return string
     * @throws \yii\base\NotSupportedException
     */
    public function buildPlaceholderName($type, $integratedName)
    {
        if (ActivePlaceholder::TYPE_WIDGET === $type) {
            return PlaceholderHelper::PREFIX_WIDGET . strtoupper($integratedName);
        }

        throw new NotSupportedException("Type '$type' not supported");
    }

    /**
     * @param $type
     * @param $placeholderName
     * @return string
     * @throws \yii\base\NotSupportedException
     */
    public function buildIntegratedName($type, $placeholderName)
    {
        if (ActivePlaceholder::TYPE_WIDGET === $type) {
            return strtolower(str_replace(PlaceholderHelper::PREFIX_WIDGET, '', $placeholderName));
        }

        throw new NotSupportedException("Type '$type' not supported");
    }

    /**
     * Returns state value when active placeholder can be activated on site
     * @param ActivePlaceholder $placeholder
     * @return bool
     * @throws \yii\base\NotSupportedException
     */
    public function canActivatePlaceholder(ActivePlaceholder $placeholder)
    {
        $config = $this->getPlaceholderConfig($placeholder);
        return !empty($config);
    }

    /**
     * @param ActivePlaceholder $placeholder
     * @return bool
     */
    final public function activatePlaceholder(ActivePlaceholder $placeholder)
    {
        Yii::trace("Placeholder '{$placeholder->getName()}' activation", __METHOD__);

        $config = $this->getPlaceholderConfig($placeholder);
        $this->ensurePlaceholderConfig($config);

        $transaction = $placeholder->getDb()->beginTransaction();
        try {
            $this->activatePlaceholderInternal($placeholder, $config);
            $transaction->commit();
            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            $placeholder->content = $placeholder->getOldAttribute('content');
            Yii::error($e, __METHOD__);
            return false;
        }
    }

    /**
     * @param ActivePlaceholder $placeholder
     * @param array $config
     */
    protected function activatePlaceholderInternal(ActivePlaceholder $placeholder, array $config)
    {
        $this->ensurePlaceholderRequirements($placeholder, $config);
        if ($placeholder instanceof WidgetPlaceholder) {
            $this->activateAsWidgetPlaceholder($placeholder);
        }
        $routes = ArrayHelper::getValue($config, 'routes', ['%/%']);
        $this->assignPlaceholderRoutes($placeholder, $routes);
    }

    /**
     * @param ActivePlaceholder $placeholder
     */
    protected function activateAsWidgetPlaceholder(ActivePlaceholder $placeholder)
    {
        $name = $placeholder->getName();
        Yii::trace("Placeholder '$name' activation as widget", __METHOD__);

        $widgetDirName = $this->buildIntegratedName(ActivePlaceholder::TYPE_WIDGET, $name);
        $class = 'widgets_dir\\' . $widgetDirName . '\Widget';
        Yii::info("Placeholder '$name' widget class: $class", __METHOD__);
        $widget = Yii::$container->get($class);
        $widget->setIsActive(true);

        $placeholder->content = serialize($widget);
        $placeholder->save(false);
        $placeholder->setWidget($widget);
    }

    /**
     * @return string
     */
    protected function getPlaceholderConfigFileName()
    {
        return 'placeholder.json';
    }

    /**
     * @param ActivePlaceholder $placeholder
     * @return array
     * @throws \yii\base\NotSupportedException
     */
    protected function getPlaceholderConfig(ActivePlaceholder $placeholder)
    {
        if (isset($this->placeholderConfig)) {
            return $this->placeholderConfig;
        }

        $name = $placeholder->getName();
        Yii::info("Retrieving config for placeholder '$name'", __METHOD__);

        if ($placeholder instanceof WidgetPlaceholder) {
            $integratedName = $this->buildIntegratedName(ActivePlaceholder::TYPE_WIDGET, $name);
            $configFileName = $this->getPlaceholderConfigFileName();
            $filepath = Yii::getAlias(implode(DIRECTORY_SEPARATOR, ['@widgets_dir', $integratedName, $configFileName]));

            if (!file_exists($filepath)) {
                Yii::warning("Config for placeholder '$name' doesn't exists: " . $filepath, __METHOD__);

                return $this->placeholderConfig = [];
            }

            $config = Json::decode(file_get_contents($filepath), true);
            Yii::info("Placeholder '$name' config" . PHP_EOL . VarDumper::dumpAsString($config), __METHOD__);
            return $config;
        }

        throw new NotSupportedException("Activate action for placeholder type '{$placeholder->type}' not implemented");
    }

    /**
     * @param array $config
     * @throws \yii\base\InvalidConfigException
     */
    protected function ensurePlaceholderConfig(array $config)
    {
        if (!isset($config['name'])) {
            throw new InvalidConfigException("Property 'name' must be set");
        }
        if (!isset($config['type']) || !in_array($config['type'], PlaceholderHelper::$types)) {
            throw new InvalidConfigException("Property 'type' must be valid type name (string)");
        }
    }

    /**
     * @param ActivePlaceholder $placeholder
     * @param array $config
     * @throws \Exception
     */
    protected function ensurePlaceholderRequirements(ActivePlaceholder $placeholder, array $config)
    {
        $parentName = $placeholder->getName();
        Yii::trace("Requirements checks for placeholder '$parentName'", __METHOD__);

        if (!isset($config['require']) || !$placeholder->isChildSupported()) {
            Yii::info("No requirements found for placeholder '$parentName'", __METHOD__);
            return;
        }

        $db = $placeholder->getDb();
        $relationTable = ActivePlaceholder::tableNameRelationChilds();
        $childNames = $config['require'];
        Yii::info("Requirements for placeholder '$parentName' found"
            . PHP_EOL . VarDumper::dumpAsString($childNames), __METHOD__);

        foreach ($childNames as $childName) {
            if (!ActivePlaceholder::find()->name($childName)->exists()) {
                throw new \Exception("Placeholder '$parentName'"
                    . " requires child '$childName', but it doesn't exists (require policy)");
            }
            $db->createCommand()
                ->insert($relationTable, ['parent_name' => $parentName, 'name' => $childName])
                ->execute();
        }
    }

    /**
     * @param ActivePlaceholder $placeholder
     * @param array $routes
     */
    protected function assignPlaceholderRoutes(ActivePlaceholder $placeholder, array $routes)
    {
        $name = $placeholder->getName();
        Yii::info("Assign routes for placeholder '$name'"
            . PHP_EOL . VarDumper::dumpAsString($routes), __METHOD__);

        $db = $placeholder->getDb();
        $relationTable = ActivePlaceholder::tableNameRelationRoutes();
        $on = ActivePlaceholder::relationTableRoutesOn();

        foreach ($routes as $route) {
            $db->createCommand()
                ->insert($relationTable, [$on[0] => $name, $on[1] => $route])
                ->execute();
        }
    }

    /**
     * @param ActivePlaceholder $placeholder
     * @return bool
     */
    final public function deactivatePlaceholder(ActivePlaceholder $placeholder)
    {
        $name = $placeholder->getName();
        Yii::trace("Placeholder '$name' deactivation", __METHOD__);

        if ($placeholder instanceof WidgetPlaceholder) {
            $widgetDirName = $this->buildIntegratedName(ActivePlaceholder::TYPE_WIDGET, $placeholder->getName());
            $widgetDir = Yii::getAlias('@widgets_dir' . DIRECTORY_SEPARATOR . $widgetDirName);
            Yii::info('Removing widget directory: ' . $widgetDir, __METHOD__);
            FileHelper::removeDirectory($widgetDir);
        }

        return $placeholder->delete();
    }
}
