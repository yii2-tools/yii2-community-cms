<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 19.02.16 9:12
 */

namespace app\modules\selftest\models;

use Yii;
use yii\caching\DummyCache;

class CacheTest extends Test
{
    public function getTitle()
    {
        return 'Cache';
    }

    /**
     * @throws \app\modules\selftest\components\TestWarningException
     */
    public function run()
    {
        Yii::trace('Checking cache actuality...', __METHOD__);

        if (Yii::$app->version !== ($oldVersion = Yii::$app->cache->get('app_version'))) {
            Yii::warning('Cache has expired, flush required ' .
                " ('$oldVersion' -> '" . Yii::$app->version . "')", __METHOD__);
            Yii::$app->cache->flush();
            Yii::$app->cache->set('app_version', Yii::$app->version);
        }
    }

    /**
     * Fixing and restoring correct application state if test failed
     */
    public function fallback()
    {
        Yii::$app->set('cache', DummyCache::className());
    }
}
