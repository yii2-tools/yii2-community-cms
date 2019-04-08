<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 20.02.16 14:47
 */

namespace app\modules\integrations\interfaces;

/**
 * Interface IntegratorInterface
 */
interface IntegratorInterface
{
    /**
     * @param array $config
     * @return IntegratorInterface $this
     * @throws \app\modules\integrations\exceptions\IntegrationException
     */
    public function setConfig($config);

    /**
     * @return bool
     * @throws \app\modules\integrations\exceptions\IntegrationException
     */
    public function integrate();

    /**
     * @return bool
     */
    public function hasErrors();

    /**
     * @return array
     */
    public function getErrors();

    /**
     * Example: ['success', 'Integration successfully completed']
     * @return array
     */
    public function getResult();
}
