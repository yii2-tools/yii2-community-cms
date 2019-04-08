<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 18.04.16 19:02
 */

namespace site\modules\plugins\components;

use Yii;
use yii\base\Component;
use yii\caching\ArrayCache;
use app\helpers\ModuleHelper;
use app\modules\integrations\interfaces\IntegrationModuleInterface;
use integrations\modules\companyName\helpers\CompanyNameHelper as IntegrationHelper;
use integrations\modules\companyName\interfaces\PluginInterface;
use site\modules\plugins\interfaces\DataManagerInterface;

class DataManager extends Component implements DataManagerInterface
{
    /**
     * Per request local cache.
     *
     * @var ArrayCache
     */
    public $cache;

    /**
     * @inheritdoc
     */
    public function __construct(ArrayCache $cache, $config = [])
    {
        $this->cache = $cache;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function getAll()
    {
        $data = $this->resolveIntegrationData();
        $plugins = [];

        foreach ($data as $row) {
            $plugins[] = $this->buildPlugin($row);
        }

        return $plugins;
    }

    /**
     * @inheritdoc
     */
    public function getByName($name)
    {
        $data = $this->resolveIntegrationData();

        if (!isset($data[$name])) {
            return null;
        }

        return $this->buildPlugin($data[$name]);
    }

    /**
     * @return array
     */
    protected function resolveIntegrationData()
    {
        if (($data = $this->cache->get(__METHOD__)) === false) {
            /** @var IntegrationModuleInterface $i9nModule */
            $i9nModule = Yii::$app->getModule(ModuleHelper::INTEGRATIONS . '/' . IntegrationHelper::COMPANY_NAME);
            $integrator = $i9nModule->configurate(IntegrationHelper::PLUGINS_GET);
            $data = $integrator->getIntegrationData(null, true, 'plugin_dir_name');
            $this->cache->set(__METHOD__, $data);
        }

        return $data;
    }

    /**
     * @param array $data integration data
     * @return PluginInterface
     */
    protected function buildPlugin(array $data)
    {
        return Yii::createObject([
            'class' => 'site\modules\plugins\components\Plugin',
            'integrationData' => $data,
        ]);
    }
}
