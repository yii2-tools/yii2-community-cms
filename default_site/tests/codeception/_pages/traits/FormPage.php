<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 11.04.16 15:53
 */

namespace tests\codeception\_pages\traits;

/**
 * Trait FormPage
 * @package tests\codeception\_pages\traits
 */
trait FormPage
{
    /**
     * Submits form on current page.
     *
     * @param array $fields
     */
    public function submit(array $fields = [])
    {
        $I = $this->actor;

        $form = [];
        $formName = $this->formName();
        $formSelector = $this->formSelector();

        foreach ($fields as $name => $value) {
            $form[$formName . '[' . $name . ']'] = $value;
        }

        $I->submitFormCsrf($formSelector, $form);
    }

    public function formSelector()
    {
        return 'form';
    }

    public function formName()
    {
        return 'Form';
    }
}
