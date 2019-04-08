<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 25.01.16 23:57
 */

namespace app\modules\booting;

use Yii;
use yii\base\BootstrapInterface;
use yii\web\Response;
use app\components\Module as BaseModule;

class Module extends BaseModule implements BootstrapInterface
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        Yii::$app->set('dispatcher', [
            'class' => 'app\modules\booting\components\Dispatcher'
        ]);

        Yii::$app->set('loader', [
            'class' => 'app\modules\booting\components\Loader'
        ]);

        Yii::$app->getResponse()->on(Response::EVENT_BEFORE_SEND, [$this, 'beforeResponseSend']);
    }

    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        Yii::beginProfile('Stage: engine \'' . $this->id . '\'', __METHOD__);

        $this->customizer->customize();
        $this->resolveCoreModule();

        Yii::endProfile('Stage: engine \'' . $this->id . '\'', __METHOD__);
    }

    /**
     * Resolve and setup core module for current request.
     */
    protected function resolveCoreModule()
    {
        Yii::$app->loader->bootstrap(Yii::$app->dispatcher->getCoreModule());
    }

    /**
     * Actions before response sending
     */
    protected function beforeResponseSend()
    {
        Yii::$app->getResponse()->headers->add('X-PJAX-Version', Yii::$app->version);
    }
}
