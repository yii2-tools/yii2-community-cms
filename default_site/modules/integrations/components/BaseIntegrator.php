<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 20.02.16 14:54
 */

namespace app\modules\integrations\components;

use Yii;
use yii\base\NotSupportedException;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\caching\DbDependency;
use app\helpers\ModuleHelper;
use app\modules\integrations\exceptions\IntegrationException;
use app\modules\integrations\interfaces\IntegrationModuleInterface;
use app\modules\integrations\interfaces\IntegratorInterface;
use app\modules\integrations\models\Integration;

/**
 * Class BaseIntegrator
 * @package app\modules\integrations\components
 */
abstract class BaseIntegrator extends Component implements IntegratorInterface
{
    /** @var \app\modules\integrations\interfaces\IntegrationModuleInterface */
    public $module;

    /** @var string */
    public $category;

    /** @var string */
    public $operation;

    /** @var array */
    public $config = [];

    /** @var bool */
    public $caching = true;

    /** @var array */
    public $context = [];

    /** @var array */
    protected $result;

    /** @var array */
    protected $errors = [];

    /** @var bool */
    private $cacheValid = true;

    /** @var array */
    protected static $cache = [];

    /**
     * @inheritdoc
     */
    public function setConfig($config)
    {
        foreach ($this->context as $property => $operations) {
            if (isset($operations[0]) && $operations[0] === '*' || in_array($this->operation, $operations)) {
                if (isset($config[$property])) {
                    $this->config[$property] = $config[$property];
                }
            }
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function integrate()
    {
        if (!isset($this->module) || !$this->module instanceof IntegrationModuleInterface) {
            throw new IntegrationException('Integration module must be defined (IntegrationModuleInterface)');
        }

        if (!isset($this->category)) {
            Yii::warning('Integration category is not defined', __METHOD__);
        }

        if (!isset($this->operation)) {
            throw new IntegrationException('Integration operation must be defined');
        }

        if (!$this->hasMethod($this->operation)) {
            throw new IntegrationException("Unknown integration operation '{$this->operation}'"
                . ' (missing method implementation?)');
        }

        $this->ensureConfig();

        return $this->integrateInternal();
    }

    /**
     * Actual integration logic.
     *
     * @return bool
     */
    protected function integrateInternal()
    {
        Yii::trace('Integration started'
            . PHP_EOL . "Vendor: '{$this->module->vendor}'"
            . PHP_EOL . "Category: '{$this->category}'"
            . PHP_EOL . "Operation: '{$this->operation}'"
            . PHP_EOL . 'Context: ' . VarDumper::dumpAsString($this->context)
            . PHP_EOL . 'Config: ' . VarDumper::dumpAsString($this->config), __METHOD__);

        $result = false;
        $transaction = Integration::getDb()->beginTransaction();
        try {
            call_user_func([$this, $this->operation]);
            if ($result = !$this->hasErrors()) {
                $transaction->commit();
            }
        } catch (\Exception $e) {
            $this->addError($e->__toString(), __METHOD__);
        }
        if (!$result) {
            $transaction->rollBack();
        }

        return $result;
    }

    /**
     * @param null $contextId
     * @param bool $multiple
     * @param bool $indexBy
     * @return array [indexBy => array]
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function getIntegrationData($contextId = null, $multiple = false, $indexBy = false)
    {
        $cacheKey = md5($this->module->vendor . $this->category . $contextId . $multiple . $indexBy);

        if ($this->cacheValid) {
            if (isset(static::$cache[$cacheKey])) {
                return static::$cache[$cacheKey];
            }
            if ($this->caching && (static::$cache[$cacheKey] = Yii::$app->cache->get($cacheKey)) !== false) {
                return static::$cache[$cacheKey];
            }
        }

        Yii::trace("Get integration data call (context_id = '$contextId'"
            . ", multiple = '" . VarDumper::dumpAsString($multiple) . "'"
            . ", indexBy = '" . VarDumper::dumpAsString($indexBy) . "')", __METHOD__);

        $integrationQuery = Integration::find()->vendor($this->module->vendor);

        if (isset($this->category)) {
            $integrationQuery->category($this->category);
        }

        if (isset($contextId)) {
            $integrationQuery->contextId($contextId);
        }

        $data = [];

        foreach ($integrationQuery->batch(10) as $integrations) {
            if (!ArrayHelper::isIndexed($integrations)) {
                $integrations = [$integrations];
            }
            $data = array_merge($data, $this->extractData($integrations));
        }

        if ($multiple && $indexBy) {
            $data = ArrayHelper::index($data, $indexBy);
        }

        $result = !empty($data)
            ? ($multiple ? $data : $data[0])
            : [];

        Yii::$app->cache->set($cacheKey, $result, 0, Yii::$container->get(DbDependency::className(), [], [
            'sql' => Integration::CACHE_DEPENDENCY,
            'reusable' => true
        ]));

        return static::$cache[$cacheKey] = $result;
    }

    /**
     * @param $contextId
     * @param null $data
     * @param null $integration
     * @return bool
     * @throws \app\modules\integrations\exceptions\IntegrationException
     */
    public function setIntegrationData($contextId, $data = null, $integration = null)
    {
        Yii::trace("Set integration data call (context_id = '$contextId')"
            . PHP_EOL . 'Data: ' . VarDumper::dumpAsString($data), __METHOD__);

        if (!isset($integration)) {
            $integration = Integration::find()->vendor($this->module->vendor);
            if (isset($this->category)) {
                $integration->category($this->category);
            }
            $integration = $integration->contextId($contextId)->one();
        }

        $this->cacheValid = false;

        if (empty($integration)) {
            if (is_null($data)) {
                throw new IntegrationException("Integration for vendor '{$this->module->vendor}'"
                     . " with context_id = '{$contextId}' not found in storage, cannot set NULL data");
            }
            $integration = Yii::createObject([
                'class' => Integration::className(),
                'vendor' => $this->module->vendor,
                'category' => $this->category,
                'context_id' => $contextId,
                'serializer' => 'json'
            ]);
        } else {
            // delete
            if (is_null($data)) {
                if ($integration->delete() === false) {
                    throw new IntegrationException('Error during set integration data call');
                }
                return true;
            }
            $previousData = $this->decodeData($integration->data, $integration->serializer);
            if (isset($previousData['previous'])) {
                unset($previousData['previous']);
            }
            $data['previous'] = $previousData;
            $data = array_replace_recursive($previousData, $data);
        }

        // insert/update
        $integration->data = $this->encodeData($data, $integration->serializer);

        if (!$integration->save(false)) {
            throw new IntegrationException('Error during set integration data call');
        }

        return true;
    }

    /**
     * @param \app\modules\integrations\models\Integration[] $integrations
     * @return array [integer => array]
     */
    protected function extractData($integrations)
    {
        $data = [];
        foreach ($integrations as $integration) {
            $data[] = $this->decodeData($integration->data, $integration->serializer);
        }

        return $data;
    }

    /**
     * @param $data
     * @param string $serializer
     * @return string
     * @throws \yii\base\NotSupportedException
     */
    protected function encodeData($data, $serializer = 'json')
    {
        if ($serializer === 'json') {
            return Json::encode($data);
        } elseif ($serializer === 'phpserialize') {
            return serialize($data);
        }

        throw new NotSupportedException("Serializer '$serializer' not supported");
    }

    /**
     * @param $data
     * @param string $serializer
     * @return array (assoc)
     * @throws \yii\base\NotSupportedException
     */
    protected function decodeData($data, $serializer = 'json')
    {
        if ($serializer === 'json') {
            return Json::decode($data, true);
        } elseif ($serializer === 'phpserialize') {
            return unserialize($data);
        }

        throw new NotSupportedException("Serializer '$serializer' not supported");
    }

    /**
     * @throws \yii\base\InvalidConfigException
     */
    protected function ensureConfig()
    {
        foreach ($this->context as $property => $operations) {
            if ((isset($operations[0]) && $operations[0] === '*' || in_array($this->operation, $operations))
                && !isset($this->config[$property])) {
                throw new InvalidConfigException("Required property '$property'"
                    . " for operation '{$this->operation}' not configured");
            }
        }
    }

    /**
     * @param $error
     */
    protected function addError($error)
    {
        $this->errors[] = $error;
    }

    /**
     * @inheritdoc
     */
    public function hasErrors()
    {
        return !empty($this->errors);
    }

    /**
     * @inheritdoc
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @inheritdoc
     */
    public function getResult()
    {
        if (isset($this->result)) {
            return $this->result;
        }

        return $this->hasErrors()
            ? ['danger', Yii::t(ModuleHelper::INTEGRATIONS, 'Integration failed')]
            : ['success', Yii::t(ModuleHelper::INTEGRATIONS, 'Integration successfully completed')];
    }
}
