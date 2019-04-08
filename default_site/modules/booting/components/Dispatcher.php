<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 27.01.16 4:48
 */

namespace app\modules\booting\components;

use Yii;
use yii\base\Component;
use yii\web\NotFoundHttpException;

/**
 * Class Dispatcher
 * Determines core module for current request
 *
 * @package app\modules\booting\components
 */
class Dispatcher extends Component
{
    /** @var string */
    protected $coreModuleId = '';

    /**
     * Getting core module for current request
     *
     * @return null|\yii\base\Module
     */
    public function getCoreModule()
    {
        if (Yii::$app->has('coreModule', true)) {
            return Yii::$app->coreModule;
        }

        $coreModuleId = null;

        // Codeception environment may not have REQUEST_URI
        // In such case this parameter is not important and will be ignored
        if (isset($_SERVER['REQUEST_URI'])) {
            try {
                $coreModuleId = $this->dispatch(Yii::$app->getRequest()->resolve(true));
            } catch (NotFoundHttpException $e) {

            }
        }

        $coreModule = Yii::$app->getModule($coreModuleId);

        if (!$coreModule) {
            Yii::info('Core module for current request not exists, using core module \''
                . Yii::$app->params['default_core_module'] . '\'', __METHOD__);
            $coreModule = Yii::$app->getModule(Yii::$app->params['default_core_module']);
        }

        Yii::$app->set('coreModule', $coreModule);

        return $coreModule;
    }

    /**
     * Getting id of core module for current request
     *
     * @return string
     */
    public function getCoreModuleId()
    {
        return $this->getCoreModule()->id;
    }

    /**
     * Getting core module id by parsing requested route
     *
     * @param string $route Requested route
     * @return string Core module Id
     */
    public function dispatch($route = '')
    {
        if (!$this->coreModuleId) {
            Yii::trace('Determining core module id', __METHOD__);
            preg_match('/^\/*([^\/?]*).*$/', $route, $matches);
            $this->coreModuleId = isset($matches[1]) ? $matches[1] : Yii::$app->params['default_core_module'];
            Yii::info('Core module id for current request resolved as \'' . $this->coreModuleId . '\'', __METHOD__);
        }
        return $this->coreModuleId;
    }
}
