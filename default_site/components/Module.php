<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 23.01.16 17:26
 */

namespace app\components;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\BootstrapInterface;
use yii\base\Module as BaseModule;
use yii\tools\filters\BreadcrumbsFilter;
use yii\tools\interfaces\ParamsHolder;
use yii\tools\params\ActiveParams;

class Module extends BaseModule implements ParamsHolder
{
    /**
     * @var array
     */
    public $bootstrap = [];

    /**
     * Enable/Disable breadcrumbs natigation via app\components\filters\BreadcrumbsFilter
     * For module itself, not affects on child modules or components
     * @var bool
     */
    public $breadcrumbs = true;

    /**
     * Array of [routes|controllers|actions] names which shouldn't have breadcrumbs
     * ['*'] means what breadcrumbs navigation disabled for all controllers and actions (direct childs)
     * For module itself, not affects on child modules
     * @var bool
     */
    public $breadcrumbsExceptRoutes = [];

    /**
     * @var string the root directory of the module config.
     */
    private $configPath;

    /**
     * Config summary
     *
     * [
     *     module => [...]
     *     some_class => [...]
     * ]
     *
     * @var array
     */
    private $config = [];

    /**
     * Config system support.
     */
    public function init()
    {
        parent::init();

        $this->configPath = $this->getBasePath() . DIRECTORY_SEPARATOR . 'config';

        if (file_exists($this->configPath)) {
            Yii::configure($this, $this->getConfig());
        }

        $this->params = Yii::$container->get(ActiveParams::className(), [], ['owner' => $this]);
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = [];

        if ($this->breadcrumbs) {
            $behaviors['breadcrumbs'] = [
                'class' => BreadcrumbsFilter::className(),
                'label' => $this->params['name'],
                'defaultRoute' => $this->defaultRoute,
                'exceptRoutes' => $this->breadcrumbsExceptRoutes,
            ];
        }

        return array_merge(parent::behaviors(), $behaviors);
    }

    /**
     * Submodules bootstrapping support.
     *
     * @param $app Application instance
     * @throws \yii\base\InvalidConfigException
     */
    public function bootstrap($app)
    {
        Yii::beginProfile('Stage: ' . 'module \'' . $this->id . '\' bootstrap', __METHOD__);

        foreach ($this->bootstrap as $class) {
            Yii::trace('Module \'' . $class . '\'' .
                ' needs to be bootstrapped as submodule of \'' . $this->id . '\'', __METHOD__);

            $component = null;

            if (is_string($class)) {
                if ($this->has($class)) {
                    $component = $this->get($class);
                } elseif ($this->hasModule($class)) {
                    $component = $this->getModule($class);
                } elseif (strpos($class, '\\') === false) {
                    throw new InvalidConfigException("Unknown bootstrapping component ID: $class");
                }
            }

            if (!isset($component)) {
                $component = Yii::createObject($class);
            }

            if ($component instanceof BootstrapInterface) {
                Yii::trace("Bootstrap with " . get_class($component) . '::bootstrap()', __METHOD__);
                $component->bootstrap($app);
                continue;
            }

            Yii::trace("Module '" . $component->getUniqueId()
                . "' loaded without ::bootstrap() execution (BootstrapInterface not implemented)", __METHOD__);
        }

        Yii::endProfile('Stage: ' . 'module \'' . $this->id . '\' bootstrap', __METHOD__);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param bool $loadedOnly
     * @param bool $load
     * @return array
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function getModules($loadedOnly = false, $load = false)
    {
        if (!$loadedOnly && $load) {
            $modules = [];
            foreach ($this->modules as $module => $configuration) {
                $modules[] = $this->getModule($module);
            }
            return $modules;
        }

        return parent::getModules($loadedOnly);
    }

    /**
     * @param string $name
     * @return array
     */
    public function getConfig($name = 'module')
    {
        if (isset($this->config[$name])) {
            return $this->config[$name];
        }

        $this->config[$name] = [];
        $defaultConfigPath = $this->configPath . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR . $name . '.php';
        $envConfigPath = $this->configPath . DIRECTORY_SEPARATOR . YII_ENV . DIRECTORY_SEPARATOR . $name . '.php';

        if (file_exists($defaultConfigPath)) {
            $this->config[$name] = array_replace_recursive($this->config[$name], require($defaultConfigPath));
        }

        if (file_exists($envConfigPath)) {
            $this->config[$name] = array_replace_recursive($this->config[$name], require($envConfigPath));
        }

        return $this->config[$name];
    }
}
