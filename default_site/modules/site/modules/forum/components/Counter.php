<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 06.05.16 15:52
 */

namespace site\modules\forum\components;

use Yii;
use yii\base\Component;
use yii\web\Response;

class Counter extends Component
{
    /**
     * @var array
     */
    public $data;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->resolveData();

        Yii::$app->getResponse()->on(Response::EVENT_BEFORE_SEND, [$this, 'saveData']);
    }

    /**
     * @param int|string $uniqueId component's unique id within application instance
     * @return bool true if inc succeed, false if this element already been counted
     */
    public function increment($uniqueId)
    {
        $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0000.0000.0000.0000';
        $agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'undefined';
        $hash = md5($ip . $agent . $uniqueId);

        if (!isset($this->data['data'][$hash])) {
            $this->data['data'][$hash] = 1;

            return true;
        }

        return false;
    }

    /**
     * Performs actual data save action.
     */
    public function saveData()
    {
        $this->getCache()->set([__FILE__, __CLASS__], $this->data);
    }

    /**
     * @return \yii\caching\Cache
     */
    public function getCache()
    {
        return Yii::$app->getCache();
    }

    protected function resolveData()
    {
        if (false === ($this->data = $this->getCache()->get([__FILE__, __CLASS__]))) {
            $this->data = $this->createData();
        }
    }

    /**
     * @return array
     */
    protected function createData()
    {
        return [
            'data' => [],
        ];
    }
}
