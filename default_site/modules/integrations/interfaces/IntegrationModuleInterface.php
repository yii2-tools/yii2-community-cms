<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 20.02.16 14:44
 */

namespace app\modules\integrations\interfaces;

/**
 * Interface IntegrationModuleInterface
 */
interface IntegrationModuleInterface
{
    /**
     * @param string $operation
     * @param array $config
     * @return \app\modules\integrations\interfaces\IntegratorInterface
     */
    public function configurate($operation, $config = []);
}
