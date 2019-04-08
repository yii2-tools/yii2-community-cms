<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 29.04.16 16:09
 */

namespace tests\codeception\_pages\admin\pages;

use app\helpers\RouteHelper;
use tests\codeception\_pages\LoginRequiredPage;
use tests\codeception\_pages\traits\FormPage;
use tests\codeception\_pages\traits\ParameterizedPage;

/**
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class EditPage extends LoginRequiredPage
{
    use ParameterizedPage, FormPage;

    public static $params = ['id'];
    public $route = [RouteHelper::ADMIN_PAGES_UPDATE];

    public function edit(array $fields = [])
    {
        $this->submit($fields);
    }

    public function formSelector()
    {
        return '.tab-content form';
    }

    public function formName()
    {
        return 'Page';
    }
}