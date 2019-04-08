<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 19.04.16 2:34
 */

namespace site\modules\plugins\components;

use Yii;
use yii\helpers\VarDumper;
use yii\web\ServerErrorHttpException;
use app\helpers\ModuleHelper;
use yii\tools\interfaces\RequestInterface;
use integrations\modules\companyName\components\Client as BaseClient;
use integrations\modules\companyName\interfaces\PluginInterface;
use site\modules\plugins\interfaces\DataManagerInterface;
use site\modules\plugins\interfaces\ContextInterface;

/**
 * Class Client
 *
 * This class contains logic ported from M_API_Plugins::call (old engine 1.0)
 * @see <gitlab link>
 *
 * @package site\modules\plugins\components
 */
class Client extends BaseClient
{
    /**
     * @var DataManagerInterface
     */
    public $dataManager;

    /**
     * @inheritdoc
     */
    public function __construct(DataManagerInterface $dataManager, RequestInterface $requester, $config = [])
    {
        $this->dataManager = $dataManager;
        parent::__construct($requester, $config);
    }

    /**
     * @inheritdoc
     */
    public function call(array $data)
    {
        try {
            $plugin = $this->dataManager->getByName($data['plugin_name']);
            /** @var ContextInterface $context */
            $context = Yii::createObject([
                'class' => 'site\modules\plugins\components\Context',
                'type' => $data['context']['type']
            ]);
            unset($data['context']);

            return $this->callInternal($plugin, $context, $data);
        } catch (\Exception $e) {
            throw new ServerErrorHttpException(Yii::t('errors', 'Engine error'), 500, $e);
        }
    }

    /**
     * Actual call logic.
     *
     * @param PluginInterface $plugin
     * @param ContextInterface $context
     * @param array $data
     * @return array
     */
    protected function callInternal(PluginInterface $plugin, ContextInterface $context, array $data)
    {
        $config = $plugin->getConfig($context->getType());
        $apiQueryKey = $config['QUERY_' . $data['query_id']];
        $apiQueryData = isset($data['query_data']) ? $data['query_data'] : [];
        $api = Yii::$app->getModule(ModuleHelper::API)->getModule($config['SITE_API_VERSION']);

        Yii::trace('Remote plugin API call'
            . PHP_EOL . 'Plugin: ' . VarDumper::dumpAsString($plugin)
            . PHP_EOL . 'Context: ' . VarDumper::dumpAsString($context)
            . PHP_EOL . 'Config: ' . VarDumper::dumpAsString($config)
            . PHP_EOL . 'Data: ' . VarDumper::dumpAsString($data), __METHOD__);

        if ($file = $plugin->getPrepareFile($context->getType(), $apiQueryKey)) {
            require_once $file;
            Yii::info("Executing prepare callback for query '$apiQueryKey'" . PHP_EOL . "File: $file", __METHOD__);
            if ($additionalData = call_user_func($apiQueryKey, $api, $config, $data)) {
                Yii::info("Additional data from prepare callback"
                    . PHP_EOL . VarDumper::dumpAsString($additionalData), __METHOD__);
                if (!empty($additionalData['errors'])) {
                    return ['status' => 0, 'data' => 0, 'errors' => $additionalData['errors']];
                }
                $apiQueryData = array_replace_recursive($apiQueryData, $additionalData);
            }
        }

        $params = [
            'client_key'     => Yii::$app->params['yii2_community_cms_site_key'],
            'api_access_key' => $plugin->getApiKey(),
            'api_version'    => $config['PLUGIN_API_VERSION'],
            'api_query_key'  => $apiQueryKey,
            'api_query_data' => $apiQueryData,
        ];

        $decodedResponse = parent::call(['url' => $config['PLUGIN_API_HREF'], 'params' => $params]);

        if ($file = $plugin->getPostpareFile($context->getType(), $apiQueryKey)) {
            require_once $file;
            Yii::info("Executing postpare callback for query '$apiQueryKey'" . PHP_EOL . "File: $file", __METHOD__);
            if ($qpResponse = call_user_func('_' . $apiQueryKey, $api, $config, $decodedResponse)) {
                Yii::info("Response modified by postpare callback"
                    . PHP_EOL . VarDumper::dumpAsString($qpResponse), __METHOD__);
                $decodedResponse = $qpResponse;
            }
        }

        return ['status' => 1, 'data' => $decodedResponse, 'errors' => 0];
    }
}
