<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 17.04.16 22:51
 */

namespace site\modules\plugins\components;

use Yii;
use yii\web\ServerErrorHttpException;
use yii\base\Component;
use app\helpers\ModuleHelper;
use site\modules\plugins\interfaces\BundleInterface;
use site\modules\plugins\assets\PluginsAsset;

/**
 * Represents assets bundle of plugin (JS and CSS files).
 *
 * This class contains logic ported from M_Plugins::getPluginComponent (old engine 1.0)
 * @see <gitlab link>
 *
 * @package site\modules\plugins\components
 */
class Bundle extends Component implements BundleInterface
{
    /** @var string */
    public $name;

    /** @var string */
    public $version;

    /** @var string */
    public $sourcePath;

    /** @var array */
    public $config;

    /** @var array */
    public $css;

    /** @var array */
    public $js;

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @inheritdoc
     */
    public function register($view)
    {
        $am = Yii::$app->getAssetManager();

        foreach ($this->css as $css) {
            list($path, $url) = $am->publish($this->sourcePath . DIRECTORY_SEPARATOR . $css);
            $view->registerCssFile($url . '?v=' . $this->version, ['depends' => [PluginsAsset::className()]]);
        }

        foreach ($this->js as $js) {
            list($path, $url) = $am->publish($this->sourcePath . DIRECTORY_SEPARATOR . $js);
            $view->registerJsFile($url . '?v=' . $this->version, ['depends' => [PluginsAsset::className()]]);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getHtml()
    {
        try {
            return Yii::$app->getView()->renderPhpFile($this->resolveTemplate(), $this->resolveParams());
        } catch (\Exception $e) {
            throw new ServerErrorHttpException(Yii::t('errors', 'Engine error'), 500, $e);
        }
    }

    /**
     * @return string
     */
    private function resolveTemplate()
    {
        return Yii::getAlias($this->sourcePath . DIRECTORY_SEPARATOR . $this->getName() . '.html');
    }

    /**
     * @return array
     */
    private function resolveParams()
    {
        $version = isset($this->config['SITE_API_VERSION']) ? $this->config['SITE_API_VERSION'] : null;
        $name = $this->getName();
        require_once Yii::getAlias($this->sourcePath . DIRECTORY_SEPARATOR . $name . '.php');

        return $name($this->resolveApi($version), $this->config);
    }

    /**
     * @param string $version
     * @return \site\modules\api\components\Module
     */
    private function resolveApi($version = null)
    {
        $module = Yii::$app->getModule(ModuleHelper::API);

        if (!isset($version)) {
            Yii::warning("Argument 'version' is not set during plugin bundle api resolve stage."
                . ' Invalid plugin config?', __METHOD__);
            $version = $module->params['version'];
        }

        $version = Yii::$app->getFormatter()->asVersionInteger($version);

        return $module->getModule($version);
    }
}
