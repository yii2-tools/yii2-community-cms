<?php

namespace app\modules\site;

use Yii;
use yii\base\BootstrapInterface;
use yii\base\Event;
use app\helpers\SessionHelper;
use app\helpers\RouteHelper;
use app\components\Module as BaseModule;
use app\modules\routing\models\Route;
use site\components\Captcha;
use design\modules\menu\models\MenuItem;

class Module extends BaseModule implements BootstrapInterface
{
    public $defaultRoute = '';

    /**
     * @param Event $event
     */
    public static function onRouteDelete(Event $event)
    {
        $menuItems = MenuItem::find()
            ->andWhere(['=', 'is_route', 1])
            ->andWhere(['=', 'url_to', $event->sender->id])
            ->all();

        foreach ($menuItems as $menuItem) {
            $menuItem->delete();
        }
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->captcha->on(Captcha::EVENT_CAPTCHA_ENABLED, [$this, 'setAjaxRedirect']);
        $this->captcha->on(Captcha::EVENT_CAPTCHA_DISABLED, [$this, 'setAjaxRedirect']);

        Event::on(Route::className(), Route::EVENT_AFTER_DELETE, [static::className(), 'onRouteDelete']);
    }

    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        parent::bootstrap($app);

        Yii::$app->viewPath = $this->viewPath;
        Yii::$app->getErrorHandler()->errorAction = RouteHelper::SITE_ERROR;
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            $this->unsetAjaxRedirect();

            return true;
        }

        return false;
    }

    protected function setAjaxRedirect()
    {
        Yii::$app->getSession()->set(SessionHelper::AJAX_REDIRECT, Yii::$app->getRequest()->getUrl());
    }

    protected function unsetAjaxRedirect()
    {
        Yii::$app->getSession()->remove(SessionHelper::AJAX_REDIRECT);
    }
}
