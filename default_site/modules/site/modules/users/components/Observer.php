<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 01.05.16 22:20
 */

namespace site\modules\users\components;

use Yii;
use yii\base\Component;
use yii\web\Response;
use site\modules\users\interfaces\ObserverInterface;

/**
 * Performs monitoring of count of all users (authorized/non-authorized)
 * per application instance.
 * @package site\modules\users\components
 * @since 2.0.0
 */
class Observer extends Component implements ObserverInterface
{
    const REFRESH_FREQUENCY = 600;  // sec.

    /**
     * Array with online data statistics (stored in cache).
     *
     * ```
     * [
     *     'expire_at': timestamp,
     *     'count': int,
     *     'data': [
     *         md5(IP + Browser) => [
     *             'activity_at': timestamp
     *         ],
     *         ...
     *     ]
     * ]
     * ```
     *
     * @var array
     */
    private $onlineData;

    /**
     * @var int
     */
    private $time;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->time = time();
        $this->resolveOnlineData();

        Yii::$app->getResponse()->on(Response::EVENT_BEFORE_SEND, [$this, 'saveOnlineData']);
    }

    public function resolveOnlineData()
    {
        if (($this->onlineData = $this->getCache()->get([__FILE__, __CLASS__])) === false) {
            $this->onlineData = $this->createOnlineData();
        }
    }

    /**
     * @inheritdoc
     */
    public function updateOnlineData($identity = null)
    {
        $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0000.0000.0000.0000';
        $agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'undefined';
        $hash = md5($ip . $agent);

        if (!isset($this->onlineData['data'][$hash])) {
            ++$this->onlineData['count'];
        }

        $this->onlineData['data'][$hash] = ['activity_at' => $this->time];
    }

    /**
     * Performs actual online data save action.
     */
    public function saveOnlineData()
    {
        if ($this->onlineData['expire_at'] < $this->time) {
            $this->refreshOnlineData();
        }

        $this->getCache()->set([__FILE__, __CLASS__], $this->onlineData);
    }

    /**
     * @inheritdoc
     */
    public function getOnlineCount()
    {
        return $this->onlineData['count'];
    }

    /**
     * @return \yii\caching\Cache
     */
    public function getCache()
    {
        return Yii::$app->getCache();
    }

    /**
     * @return array
     */
    protected function createOnlineData()
    {
        return [
            'expire_at' => 0,
            'count' => 0,
            'data' => [],
        ];
    }

    protected function refreshOnlineData()
    {
        $notExpiredThreshold = $this->time - static::REFRESH_FREQUENCY;
        $newExpireAt = $this->time + static::REFRESH_FREQUENCY;
        $newData = [];

        foreach ($this->onlineData['data'] as $hash => $record) {
            if ($record['activity_at'] > $notExpiredThreshold) {
                $newData[$hash] = $record;

                continue;
            }

            --$this->onlineData['count'];
        }

        $this->onlineData['expire_at'] = $newExpireAt;
        $this->onlineData['data'] = $newData;
    }
}
