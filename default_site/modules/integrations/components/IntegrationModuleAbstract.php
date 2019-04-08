<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 20.02.16 16:38
 */

namespace app\modules\integrations\components;

use Yii;
use app\components\Module as BaseModule;
use app\modules\integrations\exceptions\IntegrationException;
use app\modules\integrations\interfaces\IntegrationModuleInterface;
use app\modules\integrations\interfaces\IntegratorInterface;

/**
 * Class IntegrationModuleAbstract
 * @package app\modules\integrations\components
 */
abstract class IntegrationModuleAbstract extends BaseModule implements IntegrationModuleInterface
{
    /** @var string */
    public $vendor;

    /**
     * @inheritdoc
     */
    public function init()
    {
        if (!isset($this->vendor)) {
            throw new IntegrationException('Integration vendor must be defined');
        }

        parent::init();
    }

    /**
     * Default implementation of configuration method
     * @inheritdoc
     */
    public function configurate($operation, $config = [])
    {
        // category/operation
        if (($pos = strpos($operation, '/')) !== false) {
            $integratorId = substr($operation, 0, $pos);
            if (!$this->has($integratorId) || !$this->$integratorId instanceof IntegratorInterface) {
                throw new IntegrationException("Integrator '$integratorId'"
                    . ' must be implemented as instanceof IntegratorInterface'
                    . ' and configured as integration module component');
            }
            $this->$integratorId->module = $this;
            $this->$integratorId->category = $integratorId;
            $this->$integratorId->operation = substr($operation, $pos + 1);
            return $this->$integratorId->setConfig($config);
        }

        throw new IntegrationException("Integration module '{$this->vendor}'"
            . " must have implemented integrator (via IntegratorInterface) for operation '$operation'");
    }
}
