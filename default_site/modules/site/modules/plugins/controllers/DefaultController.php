<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 19.03.2016 13:43
 * via Gii Module Generator
 */

namespace site\modules\plugins\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use app\helpers\ModuleHelper;
use app\modules\site\components\Controller;
use site\modules\plugins\interfaces\DataManagerInterface;
use site\modules\plugins\interfaces\BundleManagerInterface;
use site\modules\plugins\interfaces\ContextInterface;
use site\modules\plugins\components\Context;

class DefaultController extends Controller
{
    /**
     * @var DataManagerInterface
     */
    public $pluginDataManager;

    /**
     * @var BundleManagerInterface
     */
    public $pluginBundleManager;

    /**
     * @inheritdoc
     */
    public function __construct(
        $id,
        $module,
        DataManagerInterface $pluginDataManager,
        BundleManagerInterface $pluginBundleManager,
        $config = []
    ) {
        $this->pluginDataManager = $pluginDataManager;
        $this->pluginBundleManager = $pluginBundleManager;
        parent::__construct($id, $module, $config);
    }

    /**
     * @param $name 'plugin_dir_name' integration parameter
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionShow($name)
    {
        $plugin = $this->pluginDataManager->getByName($name);

        if (empty($plugin) || 1 !== $plugin->getStatus()) {
            throw new NotFoundHttpException(
                Yii::t(ModuleHelper::PLUGINS, 'Plugin not exists or not activated yet') . '.'
            );
        }

        /** @var ContextInterface $context */
        $context = Yii::createObject([
            'class' => 'site\modules\plugins\components\Context',
            'type' => Context::TYPE_PAGE
        ]);

        $bundle = $this->pluginBundleManager->build($plugin, $context);
        $bundle->register($this->getView());

        return $this->render('show', [
            'name' => $bundle->getName(),
            'content' => $bundle->getHtml()
        ]);
    }
}
