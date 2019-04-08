<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 13.03.2016 08:01
 * via Gii Module Generator
 */

namespace site\modules\design;

use Yii;
use yii\base\BootstrapInterface;
use yii\web\Response;
use yii\base\ViewRenderer;
use app\modules\site\components\Module as BaseModule;
use site\modules\design\components\View;
use site\modules\design\components\DesignContext;

class Module extends BaseModule implements BootstrapInterface
{
    /**
     * Routes excluded from design context (site rendering system)
     * @var array
     */
    public $exceptRoutes = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        Yii::$container->setSingleton(
            'site\modules\design\interfaces\ContextInterface',
            DesignContext::className()
        );

        $this->set('view', [
            'class' => View::className(),
            'renderers' => [
                $this->params['view_extension'] => $this->getRenderer(),
            ],
            'defaultExtension' => $this->params['view_extension'],
        ]);
    }

    public function bootstrap($app)
    {
        if (!$this->rejectDesignContext()) {
            $this->assignDesignContext();
        }

        parent::bootstrap($app);
    }

    /**
     * @return View
     */
    public function getView()
    {
        return $this->get('view');
    }

    /**
     * [[ViewRenderer]] with implemented PlaceholderRendererInterface
     * @return ViewRenderer
     */
    public function getRenderer()
    {
        return $this->get('renderer');
    }

    /**
     * Activate design context (site rendering system) for current route
     */
    protected function assignDesignContext()
    {
        Yii::$app->set('view', $this->getView());
        Yii::$app->getResponse()->on(Response::EVENT_BEFORE_SEND, [$this, 'appendFooter']);
    }

    /**
     * Appending footer to page content (in case of special site rendering system)
     */
    protected function appendFooter()
    {
        $response = Yii::$app->getResponse();
        $html = $this->getView()->renderFile('@app/views/engine/footer.php');
        $response->data = str_replace('</body>', $html . '</body>', $response->data);
    }

    /**
     * Cancel custom design content rendering (e.g. for gii module context)
     */
    protected function rejectDesignContext()
    {
        foreach ($this->exceptRoutes as $route) {
            if (false !== strpos(Yii::$app->requestedRoute, $route)) {
                return true;
            }
        }

        return false;
    }
}
