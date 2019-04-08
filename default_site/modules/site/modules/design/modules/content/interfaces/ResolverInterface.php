<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 31.03.16 10:42
 */

namespace design\modules\content\interfaces;

interface ResolverInterface
{
    /**
     * Inspects file for placeholders using, returns used placeholders array
     *
     * @param string $filepath  path to file be inspected
     * @param bool $cache   is cache enabled
     * @return array
     */
    public function inspect($filepath, $cache = true);
}
