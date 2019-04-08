<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 28.01.16 4:36
 */

namespace app\modules\selftest\models;

class TestResult
{
    /** @var int */
    protected $status;

    /** @var string */
    public $title;

    const SUCCESS = 0b0;
    const FAIL = 0b1;

    public function __construct($title = '', $status = self::FAIL)
    {
        $this->title = $title;
        $this->setStatus($status);
    }

    /**
     * @param $status SUCCESS|FAIL
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return int
     */
    public function status()
    {
        return $this->status;
    }

    /**
     * Build test result details.
     */
    public function details()
    {
        return $this->title . ': ' . ($this->isSuccess() ? 'OK' : 'ERROR');
    }

    /**
     * Shortcut for status() checking
     * Return test result status.
     * @return bool
     */
    public function isSuccess()
    {
        return $this->status() == self::SUCCESS;
    }
}
