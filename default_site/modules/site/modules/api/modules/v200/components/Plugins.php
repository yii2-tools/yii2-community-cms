<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 18.04.16 6:12
 */

namespace api\modules\v200\components;

use Yii;
use yii\helpers\VarDumper;
use yii\base\Component;
use site\modules\plugins\interfaces\DataManagerInterface;
use site\modules\plugins\Finder;
use site\modules\plugins\models\PluginData;

/**
 * API component 'plugins'
 *
 * Port from API v3 (engine 1.0)
 * @see <gitlab link>
 *
 * @package api\modules\v200\components
 */
class Plugins extends Component
{
    /**
     * @var Finder
     */
    public $finder;

    /**
     * @var DataManagerInterface
     */
    public $pluginDataManager;

    /**
     * @inheritdoc
     */
    public function __construct(Finder $finder, DataManagerInterface $pluginDataManager, $config = [])
    {
        $this->finder = $finder;
        $this->pluginDataManager = $pluginDataManager;
        parent::__construct($config);
    }

    /**
     * @param string $pluginKey
     * @param array $whereStructArray
     * @return array
     */
    public function getLocalData($pluginKey, $whereStructArray = [])
    {
        Yii::trace(VarDumper::dumpAsString(func_get_args()), __METHOD__);

        $condition = ['=', 'plugin_key', $pluginKey];

        foreach ($whereStructArray as $pd_key_num => $value) {
            $condition = ['and', $condition, ['=', 'pd_key' . $pd_key_num, $value]];
        }

        $models = $this->finder->findPluginData($condition, true);
        $pluginDataArray = [];

        foreach ($models as $model) {
            $pluginDataArray[] = $model->toArray();
        }

        return $pluginDataArray;
    }

    /**
     * @param string $pluginKey
     * @param array $dataStructArray
     * @return mixed
     */
    public function setLocalData($pluginKey, $dataStructArray)
    {
        Yii::trace(VarDumper::dumpAsString(func_get_args()), __METHOD__);

        $condition = ['=', 'plugin_key', $pluginKey];
        $attributes = [];

        foreach ($dataStructArray as $ds) {
            $attributes['pd_key' . $ds['key_num']] = $ds['content'];
            if (!empty($ds['where'])) {
                $condition = ['and', $condition, ['=', 'pd_key' . $ds['key_num'], $ds['content']]];
            }
        }

        if (!($pluginData = $this->finder->findPluginData($condition))) {
            $pluginData = Yii::createObject(PluginData::className());
            $pluginData->plugin_key = $pluginKey;
        }

        $pluginData->setAttributes($attributes);

        return $pluginData->save(false);
    }

    /**
     * @param string $pluginName
     * @return array|bool
     */
    public function getInfo($pluginName)
    {
        if (!($plugin = $this->pluginDataManager->getByName($pluginName))) {
            return false;
        }

        return $plugin->getIntegrationData();
    }

    /**
     * @param string $plugin_name
     * @return bool
     */
    public function exists($plugin_name)
    {
        return false !== $this->getInfo($plugin_name);
    }

    public function request($plugin_name, $query_id, $query_data, $type = 1)
    {
        // @todo

        throw new \NotSupportedException('Not implemented');

//        $input_data = array(
//            'name'      => $plugin_name,
//            'f'         => $type,
//            'query_id'  => $query_id,
//            'data'      => $query_data
//        );
//
//        // force call plugin API.
//        return M_API_Plugins::call( $input_data, true );
    }
}
