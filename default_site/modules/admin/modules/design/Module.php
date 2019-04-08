<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 21.03.2016 16:39
 * via Gii Module Generator
 */

namespace admin\modules\design;

use Yii;
use yii\base\BootstrapInterface;
use yii\filters\AccessControl;
use admin\modules\design\helpers\ModuleHelper;
use app\modules\admin\components\Module as BaseModule;
use app\modules\services\interfaces\ManagerInterface;
use app\modules\services\helpers\ServiceHelper;

class Module extends BaseModule implements BootstrapInterface
{
    const SERVICE = ServiceHelper::PACK_BASE;

    /**
     * @var ManagerInterface
     */
    public $servicesManager;

    /**
     * @inheritdoc
     */
    public function __construct($id, $parent = null, ManagerInterface $servicesManager, $config = [])
    {
        $this->servicesManager = $servicesManager;
        parent::__construct($id, $parent, $config);
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    // activated service requirement rule
                    [
                        'allow' => false,
                        'controllers' => [
                            ModuleHelper::ADMIN_DESIGN . '/default',
                            ModuleHelper::ADMIN_DESIGN_PACKS . '/default',
                        ],
                        'matchCallback' => function () {
                            return !$this->servicesManager->isActive(static::SERVICE);
                        },
                        'denyCallback' => $this->servicesManager->buildDenyCallback(static::SERVICE),
                    ],
                    [
                        'allow' => true,
                    ],
                ],
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return array_merge([], parent::actions());
    }
}
