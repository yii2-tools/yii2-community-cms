<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 17.03.16 19:00
 */

namespace tests\codeception\_support;

use Yii;
use Codeception\Exception\ConditionalAssertionFailed;
use tests\codeception\fixtures\UserFixture;

abstract class UserActor extends \Codeception\Actor
{
    public $credentials = [];
    public $email;

    /**
     * @param string $reason
     * @throws \Codeception\Exception\ConditionalAssertionFailed
     */
    public function fail($reason)
    {
        $this->comment($reason);
        throw new ConditionalAssertionFailed();
    }

    /**
     * Setting up user with unconfirmed credentials
     */
    public function amNotConfirmed()
    {
        $notConfirmedUser = $this->getActiveFixture('user')->getModel('notConfirmedUser');
        $this->credentials = [
            'username' => $notConfirmedUser->username,
            'password' => UserFixture::PASSWORD,
        ];
        $this->email = $notConfirmedUser->email;
    }

    /**
     * Logout from account if logged in shortcut
     */
    public function amLogoutIfLoggedIn()
    {
        try {
            $this->dontSee('Logout');
        } catch (\Exception $e) {
            $this->amLogout();
        }
    }

    /**
     * Logout from account shortcut
     */
    public function amLogout()
    {
        $this->clickViaPostCsrf('a:contains(Logout)');
    }

    /**
     * @param string $link
     * @param string $attribute
     */
    public function clickViaPostCsrf($link, $params = [], $attribute = 'href')
    {
        $this->amOnPageViaPostCsrf($this->grabAttributeFrom($link, $attribute), $params);
    }

    /**
     * @param string $page
     * @param array $params
     */
    public function amOnPageViaPostCsrf($page, $params = [])
    {
        $this->amOnPageViaPost($page, array_merge($params, $this->getCsrf()));
    }

    /**
     * @param $selector
     * @param $params
     * @param $button
     */
    public function submitFormCsrf($selector, $params, $button = null)
    {
        $this->submitForm($selector, array_merge($params, $this->getCsrf()), $button);
    }

    /**
     * Get csrf param from meta tag in page source
     * @return array
     */
    private function getCsrf()
    {
        $csrfParam = $this->grabAttributeFrom('meta[name="csrf-param"]', 'content');
        $csrfToken = $this->grabAttributeFrom('meta[name="csrf-token"]', 'content');
        return [
            $csrfParam => $csrfToken
        ];
    }
}