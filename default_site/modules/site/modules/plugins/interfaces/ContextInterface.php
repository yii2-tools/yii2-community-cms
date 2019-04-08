<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 17.04.16 19:46
 */

namespace site\modules\plugins\interfaces;

interface ContextInterface
{
    /**
     * Returns plugin deployment type (e.g. as page, as widget, etc.)
     *
     * @return int
     */
    public function getType();

    /**
     * Setting up plugin deployment type.
     *
     * @param string $type
     */
    public function setType($type);
}
