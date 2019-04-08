<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 12.04.16 18:09
 */

namespace integrations\modules\companyName\interfaces;

/**
 * Interface for interacting with remote companyName services (e.g. plugins).
 *
 * @package integrations\modules\companyName\interfaces
 */
interface ClientInterface
{
    /**
     * Call remote API.
     *
     * @param array $data request data
     * @return array response data in format {"status": 1|0, "data":{}, "errors":""|[]}
     */
    public function call(array $data);
}
