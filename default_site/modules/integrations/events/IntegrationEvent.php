<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 22.04.16 2:22
 */

namespace app\modules\integrations\events;

use yii\base\Event;

class IntegrationEvent extends Event
{
    /**
     * @var string
     */
    public $vendor;

    /**
     * @var string
     */
    public $category;

    /**
     * @var array
     */
    public $context;

    /**
     * @var array
     */
    public $config;

    /**
     * @var string
     */
    public $integrationContextId;

    /**
     * @var array
     */
    public $integrationData;
}
