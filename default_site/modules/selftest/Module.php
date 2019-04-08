<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 28.01.16 3:14
 */

namespace app\modules\selftest;

use Yii;
use yii\base\Application;
use yii\base\Module as BaseModule;
use yii\base\BootstrapInterface;
use app\modules\selftest\components\TestSequence;
use app\modules\selftest\models\RedisTest;
use app\modules\selftest\models\CacheTest;
use app\modules\selftest\models\DbTest;

/**
 * Performs Power-on self-test for current instance of application
 * <https://en.wikipedia.org/wiki/Power-on_self-test>
 *
 * Class Module
 * @package app\modules\selftest
 */
class Module extends BaseModule implements BootstrapInterface
{
    /**
     * Bootstrap method to be called during application bootstrap stage.
     * @param Application $app the application currently running
     */
    public function bootstrap($app)
    {
        Yii::beginProfile('Stage: engine \'' . $this->id . '\'', __METHOD__);

        $testSequence = new TestSequence();

        if (YII_ENV_TEST || YII_ENV_PROD) {
            $testSequence->addTest(new RedisTest());
        }

        $testSequence->addTest(new CacheTest());
        $testSequence->addTest(new DbTest());

        $testSequence->start();

        Yii::endProfile('Stage: engine \'' . $this->id . '\'', __METHOD__);
    }
}
