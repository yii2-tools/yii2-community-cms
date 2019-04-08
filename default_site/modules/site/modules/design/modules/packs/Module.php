<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 28.03.2016 21:49
 * via Gii Module Generator
 */

namespace design\modules\packs;

use Yii;
use yii\base\BootstrapInterface;
use yii\base\Theme;
use app\modules\site\components\Module as BaseModule;
use site\modules\design\helpers\ModuleHelper;
use design\modules\packs\components\PathFilter;

class Module extends BaseModule implements BootstrapInterface
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->initDesignPack();
    }

    /**
     * Filter for paths in design packs.
     *
     * Some paths (e.g. global project footer) should be ignored during view rendering stage.
     * This component helps to prevent customizing some templates and forces template engine
     * to use default implementation of ignored templates.
     *
     * @return PathFilter
     */
    public function getPathFilter()
    {
        return $this->get('pathFilter');
    }

    /**
     * Init current active design pack for rendering system.
     */
    protected function initDesignPack()
    {
        Yii::setAlias('@design_packs_dir', $this->params['design_packs_dir']);
        Yii::setAlias('@design_packs_tmp', $this->params['design_packs_tmp']);
        Yii::setAlias('@design_pack', '@design_packs_dir' . DIRECTORY_SEPARATOR . $this->params['design_pack']);
        Yii::setAlias('@templates', Yii::$app->getModule(ModuleHelper::SITE)->getViewPath());
        Yii::setAlias('@styles', '@design_pack' . DIRECTORY_SEPARATOR . 'styles');
        Yii::setAlias('@scripts', '@design_pack' . DIRECTORY_SEPARATOR . 'scripts');
        Yii::setAlias('@images', '@design_pack' . DIRECTORY_SEPARATOR . 'images');

        $this->module->getView()->theme = Yii::$container->get(Theme::className(), [], [
            'basePath' => '@design_pack/templates',
            'baseUrl' => '@web/design/' . $this->params['design_pack'],
            'pathMap' => [],
        ]);
    }
}
