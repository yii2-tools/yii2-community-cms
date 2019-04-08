<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 16.04.16 14:55
 */

namespace app\modules\services\components;

use Yii;
use yii\base\Component;
use yii\web\ForbiddenHttpException;
use app\helpers\ModuleHelper;
use app\modules\services\interfaces\ManagerInterface;
use app\modules\services\Finder;

class Manager extends Component implements ManagerInterface
{
    /**
     * @var \app\modules\services\Finder
     */
    public $finder;

    /**
     * @inheritdoc
     */
    public function __construct(Finder $finder, $config = [])
    {
        $this->finder = $finder;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function isActive($name)
    {
        return ($service = $this->finder->findService(['=', 'service_name', $name])) && !empty($service);
    }

    /**
     * @inheritdoc
     */
    public function buildDenyCallback($name)
    {
        $module = Yii::$app->getModule(ModuleHelper::SERVICES);

        if (!isset($module->params['services'][$name])) {
            throw new \LogicException("Service with name '$name'"
                . PHP_EOL . " doesn't registered in module '{$module->getUniqueId()}'");
        }

        $description = $module->params['services'][$name];

        return function () use ($description) {
            $description = Yii::t(ModuleHelper::SERVICES, $description);
            $message = Yii::t('errors', "Service '{0}' should be activated to perform this action", $description);
            throw new ForbiddenHttpException($message);
        };
    }
}
