<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 16.04.16 15:00
 */

namespace app\modules\services\interfaces;

interface ManagerInterface
{
    /**
     * Returns true whenever service with $name is activated on site.
     *
     * @param string $name service name
     * @return boolean
     */
    public function isActive($name);

    /**
     * Returns callable object to be called
     * if some third-party component wants to deny access
     * due to service doesn't active on site.
     *
     * @param string $name service name
     * @return callable
     */
    public function buildDenyCallback($name);
}
