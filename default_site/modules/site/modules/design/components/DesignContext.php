<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 13.03.16 8:12
 */

namespace site\modules\design\components;

use Yii;
use yii\base\Application;
use yii\base\Component;
use yii\caching\ArrayCache;
use yii\caching\ChainedDependency;
use yii\caching\DbDependency;
use yii\caching\ExpressionDependency;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use app\modules\services\interfaces\ManagerInterface;
use app\modules\services\helpers\ServiceHelper;
use site\modules\design\helpers\ModuleHelper;
use site\modules\design\interfaces\ContextInterface;
use design\modules\content\helpers\PlaceholderHelper;
use design\modules\content\models\ActivePlaceholder;
use design\modules\content\interfaces\PlaceholderInterface;

class DesignContext extends Component implements ContextInterface
{
    /**
     * Cache for current request execution time.
     * @var ArrayCache
     */
    private $localCache;

    /**
     * @var bool
     */
    private $isConfigured = false;

    /**
     * @var ManagerInterface
     */
    protected $servicesManager;

    /**
     * @inheritdoc
     */
    public function __construct(ManagerInterface $servicesManager, $config = [])
    {
        $this->servicesManager = $servicesManager;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->localCache = Yii::$container->get(ArrayCache::className());
    }

    /**
     * @param ManagerInterface $servicesManager
     */
    public function setServicesManager(ManagerInterface $servicesManager)
    {
        $this->servicesManager = $servicesManager;
    }

    /**
     * @inheritdoc
     */
    public function configure($view)
    {
        if (!$this->isConfigured) {
            $this->configureDesignContext();
        }

        return $this->explicitExtension($view);
    }

    /**
     * Performs design context configuration
     */
    protected function configureDesignContext()
    {
        Yii::info('Configuring design context', __METHOD__);

        $this->configureLayout();
        $this->configureTheme();

        $this->isConfigured = true;
    }

    /**
     * Performs layout configuration
     * in context of custom design packs before rendering stage
     */
    protected function configureLayout()
    {
        Yii::$app->layout = $this->explicitExtension(Yii::$app->layout);
    }

    /**
     * Performs Application View Theme configuration
     * in context of custom design packs before rendering stage
     */
    protected function configureTheme()
    {
        $module = Yii::$app->controller->module;

        while (!$module instanceof Application) {
            $this->configureThemePathMap($module);
            $module = $module->module;
        }
    }

    /**
     * Creating and registering views/layouts pathMap for module
     * @param \yii\base\Module $module
     */
    protected function configureThemePathMap($module)
    {
        $viewPath = '';

        if (!($module->module instanceof Application)) {
            $viewPath = DIRECTORY_SEPARATOR . Yii::$app->getFormatter()->asRoute($module->getUniqueId(), 2);
        }

        $baseViewPath = $module->getViewPath();
        $baseLayoutPath = $module->getLayoutPath();
        $pathMap = $this->buildPathMap($viewPath, $baseViewPath, $baseLayoutPath);

        Yii::info("View theme config for module '{$module->getUniqueId()}'"
            . PHP_EOL . 'Base views path: ' . $baseViewPath
            . PHP_EOL . 'Base layouts path: ' . $baseLayoutPath
            . PHP_EOL . 'Path map: ' . VarDumper::dumpAsString($pathMap), __METHOD__);

        $view = $this->getView();
        $view->theme->pathMap = array_merge($view->theme->pathMap, $pathMap);
    }

    /**
     * @param string $viewPath
     * @param string $baseViewPath
     * @param string $baseLayoutPath
     * @return array
     */
    protected function buildPathMap($viewPath, $baseViewPath, $baseLayoutPath)
    {
        $themes = [
            '@design_packs_dir/default/templates' . $viewPath
        ];

        if ($this->servicesManager->isActive(ServiceHelper::PACK_BASE)) {
            array_unshift($themes, '@design_pack/templates' . $viewPath);
        }

        $pathMap = [
            $baseViewPath => $themes,
            $baseViewPath . DIRECTORY_SEPARATOR . 'default' => $themes,
            $baseLayoutPath => $themes,
        ];

        return $pathMap;
    }

    /**
     * @return View
     */
    protected function getView()
    {
        return Yii::$app->getModule(ModuleHelper::DESIGN)->getView();
    }

    /**
     * Returns filepath with extension (defaults will be applied if ext doesn't set)
     * (design context requires explicit extensions for custom templates rendering)
     *
     * @param string $file
     * @return string
     */
    protected function explicitExtension($file)
    {
        if (!pathinfo($file, PATHINFO_EXTENSION)) {
            $file .= '.' . Yii::$app->getView()->defaultExtension;
        }

        return $file;
    }

    /**
     * @inheritdoc
     */
    public function getGlobalPlaceholders($view)
    {
        if (!$this->isConfigured) {
            throw new \LogicException('Design context should be configured before rendering stage');
        }

        /** @var \design\modules\content\Module $module */
        $module = Yii::$app->getModule(ModuleHelper::DESIGN_CONTENT);
        $placeholders = [];

        // Placeholders available for current route.
        $available = ArrayHelper::index($this->getRoutePlaceholders(), 'name');

        // Placeholders used in template by end-user.
        $used = $module->getResolver()->inspect($view);

        /** @var PlaceholderInterface $placeholder */
        foreach ($used as $name) {
            if (!array_key_exists($name, $available)) {
                Yii::warning("Placeholder '$name' used in template '$view'"
                    . " but not available for current route '" . Yii::$app->requestedRoute . "'", __METHOD__);
                continue;
            }

            $placeholder = $available[$name];

            if ($placeholder->isChildOnly()) {
                Yii::warning("Trying to use child-only placeholder '$name'"
                    . " as global placeholder in template '$view'", __METHOD__);
                continue;
            }

            $placeholders[$name] = $placeholder->evaluate();
        }

        Yii::info("Global placeholders for template '$view' generated"
            . PHP_EOL . VarDumper::dumpAsString(array_keys($placeholders)), __METHOD__);

        return $placeholders;
    }

    /**
     * Returns all available placeholders for current route
     * Placeholders from result array can be used in all templates rendered for current route
     *
     * @return \design\modules\content\models\ActivePlaceholder[]
     */
    protected function getRoutePlaceholders()
    {
        $route = Yii::$app->requestedRoute ?: '/';
        $cacheKey = [__METHOD__, $route];

        if (($placeholders = $this->localCache->get($cacheKey)) !== false) {
            return $placeholders;
        }

        $cache = Yii::$app->getCache();

        if (($placeholders = $cache->get($cacheKey)) !== false) {
            Yii::info("Placeholders for route '$route' served from cache: "
                . PHP_EOL . VarDumper::dumpAsString(ArrayHelper::getColumn($placeholders, 'name')), __METHOD__);
            $this->localCache->set($cacheKey, $placeholders);
            return $placeholders;
        }

        $placeholders = ActivePlaceholder::find()->route($route)->all();
        Yii::info("Placeholders for route '$route' selected from database: "
            . PHP_EOL . VarDumper::dumpAsString(ArrayHelper::getColumn($placeholders, 'name')), __METHOD__);

        foreach ($placeholders as $placeholder) {
            PlaceholderHelper::instantiateChilds($placeholder);
        }

        $dependency = Yii::$container->get(ChainedDependency::className(), [], [
            'dependencies' => [
                Yii::$container->get(ExpressionDependency::className(), [], [
                    'expression' => 'Yii::$app->getModule(\site\modules\design\helpers\ModuleHelper::DESIGN_CONTENT)'
                        . '->params["version"]',
                    'reusable' => true,
                ]),
                Yii::$container->get(DbDependency::className(), [], [
                    'sql' => ActivePlaceholder::CACHE_DEPENDENCY,
                    'reusable' => true,
                ])
            ],
            'reusable' => true,
        ]);

        $cache->set($cacheKey, $placeholders, 0, $dependency);
        $this->localCache->set($cacheKey, $placeholders);

        return $placeholders;
    }
}
