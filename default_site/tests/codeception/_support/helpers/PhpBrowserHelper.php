<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 19.03.16 18:48
 */

namespace tests\codeception\_support\helpers;

use Codeception\Module;
use Yii;

class PhpBrowserHelper extends Module
{
    /**
     * @param string $page
     * @param array $params
     */
    public function amOnPageViaPost($page, $params = [])
    {
        $this->amOnPageViaMethod('POST', $page, $params);
    }

    /**
     * @param string $method
     * @param string $page
     * @param array $params
     */
    public function amOnPageViaMethod($method, $page, $params = [])
    {
        $this->getModule('PhpBrowser')->_loadPage($method, $page, $params);
    }
}