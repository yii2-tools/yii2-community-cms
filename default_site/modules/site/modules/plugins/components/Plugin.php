<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 17.04.16 23:14
 */

namespace site\modules\plugins\components;

use Yii;
use yii\helpers\Json;
use yii\base\Component;
use integrations\modules\companyName\interfaces\PluginInterface;

/**
 * Class Plugin
 * @package integrations\modules\companyName\components
 */
class Plugin extends Component implements PluginInterface
{
    /** @var array */
    public $integrationData;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (!isset($this->integrationData)) {
            throw new \LogicException("Property 'integrationData' must be set");
        }
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->integrationData['plugin_dir_name'];
    }

    /**
     * @inheritdoc
     */
    public function getVersion()
    {
        return $this->integrationData['active_version'];
    }

    /**
     * @inheritdoc
     */
    public function getStatus()
    {
        return isset($this->integrationData['status']) ? $this->integrationData['status'] : 0;
    }

    /**
     * @inheritdoc
     */
    public function getKey()
    {
        return $this->integrationData['plugin_key'];
    }

    /**
     * @inheritdoc
     */
    public function getApiKey()
    {
        return $this->integrationData['plugin_api_key'];
    }

    /**
     * @inheritdoc
     */
    public function getSourceDir()
    {
        return Yii::getAlias('@plugins_dir' . DIRECTORY_SEPARATOR . $this->getName());
    }

    /**
     * @inheritdoc
     */
    public function getPrepareFile($type, $queryKey)
    {
        $filepath = $this->getSourceDir()
            . implode(DIRECTORY_SEPARATOR, ['', $type, 'query_prepare', $queryKey . '.php']);

        return file_exists($filepath) ? $filepath : false;
    }

    /**
     * @inheritdoc
     */
    public function getPostpareFile($type, $queryKey)
    {
        $filepath = $this->getSourceDir()
            . implode(DIRECTORY_SEPARATOR, ['', $type, 'query_postpare', '_' . $queryKey . '.php']);

        return file_exists($filepath) ? $filepath : false;
    }

    /**
     * @inheritdoc
     */
    public function getConfig($type)
    {
        $name = $this->getName();
        $configFiles = [
            'config/' . $name . '.json',
            'config/' . $name . '_api.json',
        ];
        $configDir = $this->getSourceDir() . DIRECTORY_SEPARATOR . $type;
        $config = [];

        foreach ($configFiles as $configFile) {
            if ($contents = file_get_contents(Yii::getAlias($configDir . DIRECTORY_SEPARATOR . $configFile))) {
                $config = array_merge($config, Json::decode($contents));
            }
        }

        return $config;
    }

    /**
     * @inheritdoc
     */
    public function getIntegrationData()
    {
        return $this->integrationData;
    }
}
