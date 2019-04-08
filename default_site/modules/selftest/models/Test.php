<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 28.01.16 5:37
 */

namespace app\modules\selftest\models;

use Yii;

abstract class Test implements TestInterface
{
    /**
     * @return TestResult
     */
    public function execute()
    {
        $result = new TestResult($this->getTitle());

        try {
            $this->run();
            $result->setStatus(TestResult::SUCCESS);
        } catch (\Exception $e) {
            Yii::error($e->__toString(), __METHOD__);
            Yii::trace('Fallback for test \'' . $this->getTitle() . '\' started', __METHOD__);
            $this->fallback();
        }

        return $result;
    }

    abstract public function getTitle();
    abstract public function run();
    abstract public function fallback();
}
