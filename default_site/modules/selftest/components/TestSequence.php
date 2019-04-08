<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 28.01.16 3:23
 */

namespace app\modules\selftest\components;

use Yii;
use yii\base\Component;
use yii\log\Logger;
use app\modules\selftest\models\Test;

/**
 * Checks current environment for available params and required components.
 * Activates reserve systems (via di) if some errors occurred
 *
 * Class TestSequence
 * @package app\modules\selftest\components
 */
class TestSequence extends Component
{
    /** @var array TestResult */
    protected $results = [];

    /** @var array Test */
    protected $tests = [];

    /**
     * @param Test $test
     */
    public function addTest(Test $test)
    {
        $this->tests[] = $test;
    }

    /**
     * Starts tests loop for current environment.
     */
    public function start()
    {
        Yii::trace('Self-test started. Current environment: ' . YII_ENV, __METHOD__);

        foreach ($this->tests as $test) {
            $this->results[] = $test->execute();
        }

        list($resultMessage, $level) = $this->buildResult();

        Yii::getLogger()->log($resultMessage, $level, __METHOD__);
    }

    /**
     * Build result message and level for Logger
     * @return [$resultMessage, $level]
     */
    protected function buildResult()
    {
        $resultMessage = 'Self-test finished';
        $resultDetails = '';
        $errorsNumber = 0;

        foreach ($this->results as $result) {
            $resultDetails .= PHP_EOL . $result->details();

            if (!$result->isSuccess()) {
                ++$errorsNumber;
            }
        }

        $resultMessage .= '. ';

        if ($errorsNumber > 0) {
            $level = Logger::LEVEL_ERROR;
            $resultMessage .= 'Errors detected [FAILED]';
        } else {
            $level = Logger::LEVEL_INFO;
            $resultMessage .= 'No errors detected [SUCCESS]';
        }

        $resultMessage .= $resultDetails;

        return [$resultMessage, $level];
    }
}
