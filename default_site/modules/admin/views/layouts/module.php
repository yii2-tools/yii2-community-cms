<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 25.02.16 21:10
 */

use yii\bootstrap\Html;
use dmstr\widgets\Alert;
use app\modules\admin\components\Module as AdminModule;

if ($this->params['module'] instanceof AdminModule) {
    $this->params['menu'] = $this->params['module']->menu();
}

?>

<?= Html::beginTag('div', ['class' => 'row']) ?>
    <?= Html::beginTag('div', ['class' => 'col-md-12']) ?>
        <?= Html::beginTag('div', ['class' => 'nav-tabs-custom']) ?>
            <?php if (!empty($this->params['menu'])) : ?>
                <?= $this->render('@admin/views/layouts/module/menu') ?>
            <?php endif ?>

            <div class="tab-content">
                <?= Alert::widget() ?>

                <?= Html::beginTag('div', ['class' => 'row']) ?>
                    <?= Html::beginTag('div', ['class' => 'col-md-12']) ?>
                        <?= $content ?>
                    <?= Html::endTag('div') ?>
                <?= Html::endTag('div') ?>
            </div>
        </div>
    </div>
</div>