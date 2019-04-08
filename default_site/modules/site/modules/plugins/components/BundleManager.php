<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 17.04.16 22:10
 */

namespace site\modules\plugins\components;

use Yii;
use yii\base\Component;
use yii\helpers\Url;
use app\helpers\ModuleHelper;
use app\helpers\RouteHelper;
use app\modules\services\helpers\ServiceHelper;
use yii\tools\interfaces\ContentGeneratorInterface;
use app\modules\services\interfaces\ManagerInterface;
use integrations\modules\companyName\interfaces\PluginInterface;
use site\modules\plugins\interfaces\BundleManagerInterface;
use site\modules\plugins\interfaces\DataManagerInterface;
use site\modules\plugins\interfaces\ContextInterface;
use site\modules\plugins\Finder;
use admin\modules\users\helpers\RbacHelper;

class BundleManager extends Component implements BundleManagerInterface, ContentGeneratorInterface
{
    /**
     * @var Finder
     */
    public $finder;

    /**
     * @var ManagerInterface
     */
    public $serviceMananger;

    /**
     * @var DataManagerInterface
     */
    public $dataManager;

    /**
     * @inheritdoc
     */
    public function __construct(
        Finder $finder,
        ManagerInterface $serviceManager,
        DataManagerInterface $dataManager,
        $config = []
    ) {
        $this->finder = $finder;
        $this->serviceMananger = $serviceManager;
        $this->dataManager = $dataManager;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function build(PluginInterface $plugin, ContextInterface $context)
    {
        $name = $plugin->getName();
        $type = $context->getType();

        return Yii::createObject([
            'class' => 'site\modules\plugins\components\Bundle',
            'name' => $name,
            'version' => $plugin->getVersion(),
            'sourcePath' => $plugin->getSourceDir() . DIRECTORY_SEPARATOR . $type,
            'config' => $plugin->getConfig($type),
            'css' => [
                $name . '.css',
            ],
            'js' => [
                $name . '.js',
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function generateContent()
    {
        return $this->getMenuBlocks();
    }

    /**
     * @inheritdoc
     */
    public function getMenuBlocks()
    {
        $plugins = $this->dataManager->getAll();
        $content = [];

        foreach ($plugins as $plugin) {
            if (1 === $plugin->getStatus() && ($html = $this->getMenuBlock($plugin))) {
                $content[] = $html;
            }
        }

        return $content;
    }

    /**
     * @inheritdoc
     */
    public function getMenuBlock(PluginInterface $plugin)
    {
        $name = $plugin->getName();

        if ($name === 'events' && $this->serviceMananger->isActive(ServiceHelper::ANALYTICS)) {
            return $this->buildEventsStats($plugin);
        }

        return false;
    }

    /**
     * Fast crutch for analytics system plugin.
     *
     * @param PluginInterface $plugin
     * @return bool|string
     */
    protected function buildEventsStats(PluginInterface $plugin)
    {
        $user = Yii::$app->getUser();

        if ($user->isGuest || (!$user->can(RbacHelper::PLUGINS_EVENTS_ACCESS) && !$user->getIdentity()->isAdmin)) {
            return false;
        }

        $pluginData = $this->finder->findPluginData([
            'and', ['=', 'plugin_key', $plugin->getKey()], ['=', 'pd_key1', $user->getId()]
        ]);

        return Yii::$app->getView()->render('@plugins/views/events/menu_stats.php', [
            'url' => Url::to([RouteHelper::SITE_PLUGINS_SHOW, 'name' => $plugin->getName(), '#' => 'stats']),
            'title' => Yii::t(ModuleHelper::SERVICES, 'Analytics system'),
            'reputation' => $pluginData ? $pluginData['pd_key2'] : 0,
        ]);
    }
}
