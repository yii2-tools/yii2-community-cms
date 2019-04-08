<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 04.02.16 7:02
 */

namespace app\components\controllers;

use Yii;
use yii\base\Event;
use yii\web\Controller as BaseController;
use yii\web\View;
use yii\filters\VerbFilter;
use app\helpers\RouteHelper;

/**
 * Class WebController
 * @package app\controllers
 */
abstract class WebController extends BaseController
{
    /**
     * @var bool
     */
    public static $globalTitleApplied = false;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index'  => ['get'],
                    'view'   => ['get'],
                    'show'   => ['get'],
                    'create' => ['get', 'post'],
                    'add'    => ['get', 'post'],
                    'update' => ['get', 'put', 'post'],
                    'edit'   => ['get', 'put', 'post'],
                    'delete' => ['post', 'delete'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        Event::on(View::className(), View::EVENT_BEGIN_PAGE, [$this, 'applyGlobalTitle']);
    }

    /**
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function applyGlobalTitle()
    {
        if (!static::$globalTitleApplied && ($globalTitle = $this->globalTitle())) {
            $view = $this->getView();

            if (empty($view->title)) {
                $view->title = $globalTitle;
            } else {
                $view->title .= ' - ' . $globalTitle;
            }

            static::$globalTitleApplied = true;
        }
    }

    /**
     * @return string|null
     */
    public function globalTitle()
    {
        return null;
    }

    /**
     * @param \yii\base\Action $action
     * @param mixed $result
     * @return mixed
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function afterAction($action, $result)
    {
        if ($this->isReturnUrlChanged()) {
            $this->updateReturnUrl();
        } else {
            Yii::info('Skipping set return url', __METHOD__);
        }

        return parent::afterAction($action, $result);
    }

    public function isReturnUrlChanged()
    {
        return Yii::$app->getRequest()->isGet
                && Yii::$app->getResponse()->isOk
                && !Yii::$app->getRequest()->isAjax
                && !in_array(
                    '/' . Yii::$app->requestedRoute,
                    [
                        RouteHelper::SITE_USERS_LOGIN,
                        RouteHelper::SITE_CAPTCHA,
                        RouteHelper::SITE_USERS_RECOVERY_REQUEST,
                        RouteHelper::SITE_USERS_RECOVERY_RESET
                    ]
                );
    }

    public function updateReturnUrl()
    {
        $returnUrl = Yii::$app->getRequest()->getUrl();
        Yii::info('Set return url: ' . $returnUrl, __METHOD__);
        Yii::$app->getUser()->setReturnUrl($returnUrl);
    }
}
