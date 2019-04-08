<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 10.02.16 21:27
 *
 * @var $this   yii\web\View
 * @var $module app\components\Module
 */

use app\helpers\Html;
use app\helpers\ModuleHelper;

?>

<?= Html::beginTag('div', ['class' => 'col-xs-12 col-md-6 col-lg-4']) ?>
    <?= Html::beginTag('div', ['class' => 'box box-solid box-default box-module']) ?>
        <?= Html::beginTag('div', ['class' => 'box-header']) ?>
            <?= Html::beginTag('h3', ['class' => 'box-title']) ?>
                <?php if (isset($module->params['icon'])) : ?>
                    <?= Html::faIcon($module->params['icon']) ?>
                <?php endif ?>
                <?= $module->siteModule->params['name'] ?>
                <?= Html::beginTag('small') ?><?= Yii::t(ModuleHelper::ADMIN, 'Module')?><?= Html::endTag('small') ?>
            <?= Html::endTag('h3') ?>

            <?= Html::beginTag('div', ['class' => 'box-tools pull-right']) ?>
                <?= Html::beginTag('span', ['class' => 'label label-success']) ?>
                    <?= $module->siteModule->params['version'] ?>
                <?= Html::endTag('span') ?>
            <?= Html::endTag('div') ?>
        <?= Html::endTag('div') ?>

        <?= Html::beginTag('div', ['class' => 'box-body']) ?>
            <?php $count = 0;
            foreach ($menu as $item) :
                if (empty($item['url'])) {
                    continue;

                } else {
                    ++$count;
                } ?>
                            <?= Html::beginTag('div', ['class' => 'col-md-12' . (($count > 1) ? ' margin-top-5' : '')]) ?>
                                <?= Html::a(
                                    Html::encode(isset($item['description']) ? $item['description'] : $item['label']),
                                    $item['url']
                                ) ?>
                            <?= Html::endTag('div') ?>
                        <?php                                                                                                                                                                                                                                                                                                             endforeach ?>
        <?= Html::endTag('div') ?>
    <?= Html::endTag('div') ?>
<?= Html::endTag('div') ?>