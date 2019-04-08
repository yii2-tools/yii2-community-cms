<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 21.02.16 6:13
 */

namespace integrations\modules\companyName\components;

use Yii;
use yii\tools\interfaces\RequestInterface;
use app\modules\integrations\exceptions\IntegrationException;
use app\modules\integrations\components\BaseIntegrator;
use app\modules\integrations\events\IntegrationEvent;
use app\modules\integrations\models\Integration;
use app\helpers\ModuleHelper;

/**
 * Class Integrator
 * @package integrations\modules\companyName\components
 */
abstract class Integrator extends BaseIntegrator
{
    const EVENT_AFTER_DEACTIVATE = 'afterDeactivate';

    /**
     * Main server end-point for integration requests.
     */
    const MAIN_SERVER_AJAX_HREF = 'http://domain.ltd/ajax.php';

    /**
     * @var RequestInterface
     */
    public $requester;

    /** @var string */
    public $dataKey;

    /** @var string */
    public $dataKeyShort;

    /**
     * @inheritdoc
     */
    public function __construct(RequestInterface $requester, $config = [])
    {
        $this->requester = $requester;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    protected function integrateInternal()
    {
        // trying to get related module in site context (event handlers needs to be activated).
        Yii::$app->getModule(ModuleHelper::SITE)->getModule($this->category);

        return parent::integrateInternal();
    }

    /**
     * @param array $requestBody
     * @return array|bool false, if failed, array if all OK
     * @throws IntegrationException
     */
    protected function query($requestBody = [])
    {
        $request = [
            'action_id' => static::MAIN_SERVER_QUERY_ACTION,
            'request_body' =>
                [
                    array_merge(
                        [
                            $this->config['action'] => $this->config['queries'][$this->operation],
                            'sk' => Yii::$app->params['yii2_community_cms_site_key'],
                            'iv' => $this->module->params['version'],
                        ],
                        $requestBody
                    )
                ]
        ];

        $response = json_decode($this->requester->request(static::MAIN_SERVER_AJAX_HREF, ['r' => $request]));

        if (!isset($response->status) || intval($response->status) !== 1) {
            throw new IntegrationException('Bad response form {companyName} main server');
        }

        return $response->data;
    }

    public function get()
    {
        $response = $this->query();
        $affected = [];

        foreach ($response as $data) {
            $data = (array)$data;
            if (empty($data[$this->dataKey])) {
                throw new IntegrationException("Property '" . $this->dataKey . "' is not defined, bad response record");
            }
            $this->setIntegrationData($data[$this->dataKey], $data);
            $affected[] = $data[$this->dataKey];
        }

        $integrationsQuery = Integration::find()
            ->andWhere(['=', 'category', $this->category])
            ->andWhere(['not in', 'context_id', $affected]);

        foreach ($integrationsQuery->batch(10) as $integrations) {
            foreach ($integrations as $integration) {
                $this->setIntegrationData($integration->context_id, null, $integration);
            }
        }
    }

    public function activate()
    {
        $data = $this->getIntegrationData($this->config[$this->dataKey]);

        if (empty($data) || (isset($data['status']) && intval($data['status']) !== 0)) {
            $this->addError("Cannot activate object, no integration data or status !== 0");
            return false;
        }

        $response = $this->query([$this->dataKeyShort => $this->config[$this->dataKey]]);

        if ($response !== true) {
            $this->addError($response);
            return false;
        }

        $this->updateIntegrationData($data);
    }

    public function updateIntegrationData($data)
    {
        $this->setIntegrationData($this->config[$this->dataKey], array_merge($data, ['status' => 2]));
    }

    public function update()
    {
        $data = $this->getIntegrationData($this->config[$this->dataKey]);

        if (empty($data) || !isset($data['status']) || intval($data['status']) !== 1
            || $data['active_version'] == $data['current_version']) {
            $this->addError("Cannot update object, no integration data or status !== 1 or version is up-to-date");
            return false;
        }

        $response = $this->query([$this->dataKeyShort => $data[$this->dataKey]]);

        if ($response !== true) {
            $this->addError($response);
            return false;
        }

        $this->setIntegrationData($this->config[$this->dataKey], array_merge($data, ['status' => 3]));
    }

    public function deactivate()
    {
        $contextId = $this->config[$this->dataKey];
        $data = $this->getIntegrationData($contextId);

        if (empty($data) || !isset($data['status']) || intval($data['status']) !== 1) {
            $this->addError("Cannot deactivate object, no integration data or status !== 1");
            return false;
        }

        $response = $this->query([$this->dataKeyShort => $data[$this->dataKey]]);

        if ($response !== true) {
            $this->addError($response);
            return false;
        }

        $this->deleteIntegrationData($data);

        $event = Yii::createObject([
            'class' => IntegrationEvent::className(),
            'vendor' => $this->module->vendor,
            'category' => $this->category,
            'context' => $this->context,
            'config' => $this->config,
            'integrationContextId' => $contextId,
            'integrationData' => $data,
        ]);
        $this->trigger(static::EVENT_AFTER_DEACTIVATE, $event);
    }

    public function deleteIntegrationData($data)
    {
        $this->setIntegrationData($this->config[$this->dataKey], array_merge($data, ['status' => 4]));
    }
}
