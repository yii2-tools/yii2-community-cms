<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 21.03.2016 16:49
 * via Gii Module Generator
 */

namespace site\modules\pages\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use app\modules\site\components\Controller;
use site\modules\pages\Finder;
use site\modules\pages\models\Page;

class DefaultController extends Controller
{
    /**
     * @var Finder
     */
    public $finder;

    /**
     * @inheritdoc
     */
    public function __construct($id, $module, Finder $finder, $config = [])
    {
        $this->finder = $finder;
        parent::__construct($id, $module, $config);
    }

    public function actionShow()
    {
        $route = Yii::$app->router->getCurrentRoute();
        $condition = ['=', 'route_id', $route->getPrimaryKey()];

        /** @var Page $model */
        if (!($model = $this->finder->findPage($condition))) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        return $this->render('show', [
            'title' => $model->title,
            'content' => $model->content
        ]);
    }
}
