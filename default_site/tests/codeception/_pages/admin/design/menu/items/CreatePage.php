<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 11.04.16 15:23
 */

namespace tests\codeception\_pages\admin\design\menu\items;

use app\helpers\RouteHelper;
use tests\codeception\_pages\LoginRequiredPage;
use tests\codeception\_pages\traits\FormPage;

/**
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class CreatePage extends LoginRequiredPage
{
    use FormPage;

    public $route = [RouteHelper::ADMIN_DESIGN_MENU_ITEMS_CREATE];

    public function create(array $fields = [])
    {
        $this->submit($fields);
    }

    public function formName()
    {
        return 'ItemForm';
    }
}