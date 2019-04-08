<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 18.04.16 6:11
 */

namespace api\modules\v200\components;

use Yii;
use yii\base\Component;
use app\modules\services\interfaces\ManagerInterface;

/**
 * API component 'services'
 *
 * Port from API v3 (engine 1.0)
 * @see <gitlab link>
 *
 * @package api\modules\v200\components
 */
class Services extends Component
{
    /**
     * @var ManagerInterface
     */
    public $manager;

    /**
     * @inheritdoc
     */
    public function __construct(ManagerInterface $manager, $config = [])
    {
        $this->manager = $manager;
        parent::__construct($config);
    }

    /**
     * @param string $service_name
     * @return mixed
     */
    public function isServiceExists($service_name)
    {
        return $this->manager->isActive($service_name);
    }
}
