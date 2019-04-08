<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 30.03.16 4:47
 */

namespace app\modules\site\components;

use Yii;
use app\helpers\ModuleHelper;
use app\components\controllers\WebController;
use app\assets\ExternalAsset;
use app\modules\site\assets\SiteAsset;

/**
 * Controller class for site modules
 * @package app\modules\site\components
 */
class Controller extends WebController
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $view = $this->getView();

        SiteAsset::register($view);
        ExternalAsset::register($view);

        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function render($view, $params = [])
    {
        $moduleLayout = isset($this->module->moduleLayout) ? $this->module->moduleLayout : null;

        return !empty($moduleLayout)
            ? parent::render($moduleLayout, array_merge(['content' => parent::renderPartial($view, $params)], $params))
            : parent::render($view, $params);
    }

    /**
     * @return null|string
     */
    public function globalTitle()
    {
        return Yii::$app->getModule(ModuleHelper::DESIGN)->params['title'];
    }
}
