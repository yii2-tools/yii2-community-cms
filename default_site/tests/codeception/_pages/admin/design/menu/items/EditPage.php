<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 11.04.16 18:28
 */

namespace tests\codeception\_pages\admin\design\menu\items;

use app\helpers\RouteHelper;
use tests\codeception\_pages\LoginRequiredPage;
use tests\codeception\_pages\traits\ParameterizedPage;
use tests\codeception\_pages\traits\FormPage;

/**
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class EditPage extends LoginRequiredPage
{
    use ParameterizedPage, FormPage;

    public static $params = ['id'];

    public $route = [RouteHelper::ADMIN_DESIGN_MENU_ITEMS_UPDATE];

    public function update(array $fields = [])
    {
        $this->submit($fields);
    }

    public function formSelector()
    {
        return '.tab-content form';
    }

    public function formName()
    {
        return 'ItemForm';
    }
}