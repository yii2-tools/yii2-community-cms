<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 19.03.2016 13:43
 * via Gii Module Generator
 */

namespace site\modules\plugins;

use site\modules\plugins\models\PluginData;
use Yii;
use yii\base\Event;
use yii\filters\AccessControl;
use app\modules\site\components\Module as BaseModule;
use app\modules\integrations\events\IntegrationEvent;
use integrations\modules\companyName\interfaces\ClientInterface;
use integrations\modules\companyName\components\Integrator;
use site\modules\plugins\interfaces\DataManagerInterface;
use site\modules\plugins\interfaces\BundleManagerInterface;
use design\modules\menu\interfaces\ManagerInterface;
use admin\modules\users\helpers\RbacHelper;

class Module extends BaseModule
{
    /**
     * @var ManagerInterface
     */
    public $menuManager;

    /**
     * @inheritdoc
     */
    public function __construct($id, $parent = null, ManagerInterface $menuManager, $config = [])
    {
        $this->menuManager = $menuManager;
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
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function () {
                            $user = Yii::$app->getUser();

                            return $user->can(RbacHelper::PLUGINS_ACCESS) || $user->getIdentity()->isAdmin;
                        },
                    ]
                ],
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        Yii::$container->setSingleton(
            'site\modules\plugins\interfaces\DataManagerInterface',
            $this->getDataManager()
        );

        Yii::$container->setSingleton(
            'site\modules\plugins\interfaces\BundleManagerInterface',
            $this->getBundleManager()
        );

        Yii::$container->setSingleton('site\modules\plugins\components\Client', $this->getClient());

        $this->registerPluginsContent();

        Event::on(
            Integrator::className(),
            Integrator::EVENT_AFTER_DEACTIVATE,
            [static::className(), 'clearPluginData']
        );
    }

    /**
     * Returns Data Manager suited for parsing raw integration data.
     * Performs wrapping plugin data from external service via PluginInterface.
     *
     * @return DataManagerInterface
     */
    public function getDataManager()
    {
        return $this->get('dataManager');
    }

    /**
     * Returns Bundle Manager which can build plugins bundle
     * with css, js, html and another data for displaying purposes.
     *
     * @return BundleManagerInterface
     */
    public function getBundleManager()
    {
        return $this->get('bundleManager');
    }

    /**
     * Returns Client instance which can perform requests
     * to remote plugin API server.
     *
     * @return ClientInterface
     */
    public function getClient()
    {
        return $this->get('client');
    }

    /**
     * Registering additional content of active plugins (e.g. in menu)
     */
    protected function registerPluginsContent()
    {
        $this->menuManager->register($this->getBundleManager());
    }

    /**
     * @param IntegrationEvent $event
     */
    public static function clearPluginData(IntegrationEvent $event)
    {
        PluginData::deleteAll(['plugin_key' => $event->integrationContextId]);
    }
}
