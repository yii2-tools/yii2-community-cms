<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 28.01.16 4:41
 */

namespace app\modules\selftest\models;

use app\modules\selftest\components\TestException;
use Yii;

class RedisTest extends Test
{
    public function getTitle()
    {
        return 'Redis';
    }

    /**
     * @return TestResult
     */
    public function run()
    {
        Yii::trace('Checking redis connection...', __METHOD__);

        Yii::$app->redis->open();
    }

    /**
     * Fixing and restoring correct application state if test failed
     */
    public function fallback()
    {
        Yii::$app->set('redis', null);

        Yii::info('Configuring default components.' .
            PHP_EOL . 'Session: ' . Yii::$app->params['default_class_session'] .
            PHP_EOL . 'Cache: ' . Yii::$app->params['default_class_cache'], __METHOD__);

        Yii::$app->set('session', [
            'class' => Yii::$app->params['default_class_session']
        ]);

        Yii::$app->set('cache', [
            'class' => Yii::$app->params['default_class_cache'],
        ]);
    }
}
