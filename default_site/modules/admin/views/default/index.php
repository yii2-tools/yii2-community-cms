<?php

/**
 * @var $this   yii\web\View
 * @var $module app\modules\admin\Module
 * @var $subModule app\components\Module
 */

use yii\helpers\Html;
use app\modules\admin\components\Module as AdminModule;

$subModules = $module->getModules(false, true);

?>

<?= Html::beginTag('div', ['class' => 'row']) ?>
    <?= Html::beginTag('div', ['class' => 'col-md-12']) ?>
        <?= Html::beginTag('div', ['class' => 'row']) ?>
        <?php $count = 0; foreach ($subModules as $subModule) : ?>
            <?php if ($subModule instanceof AdminModule && ($menu = $subModule->menu())) : ?>
                <?= $this->render('submodule/container', ['module' => $subModule, 'menu' => $menu]) ?>
            <?php endif ?>
        <?php endforeach ?>
        <?= Html::endTag('div') ?>
    <?= Html::endTag('div') ?>
<?= Html::endTag('div');
