<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 18.04.16 19:04
 */

namespace site\modules\plugins\interfaces;

use integrations\modules\companyName\interfaces\PluginInterface;

interface DataManagerInterface
{
    /**
     * Returns all available objects with interface of plugin (wrappers over integration data).
     *
     * @return PluginInterface[]
     */
    public function getAll();

    /**
     * Returns object with interface of plugin (wrapper over integration data).
     *
     * @param string $name
     * @return PluginInterface|null
     */
    public function getByName($name);
}
