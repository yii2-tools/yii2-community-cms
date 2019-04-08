<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 09.04.16 14:43
 */

use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\sortable\Sortable;
use app\helpers\Html;
use app\helpers\RouteHelper;
use site\modules\design\helpers\ModuleHelper;

$this->title = Yii::t(ModuleHelper::ADMIN_DESIGN, 'Edit menu');
$this->params['breadcrumbs'][] = $this->title;

$items = [];

foreach ($models as $model) {
    $items[] = [
        'content' => $this->render('/_item', ['model' => $model]),
        'options' => [
            'class' => 'item',
        ],
    ];
}

?>

<?= Html::beginTag('div', ['class' => 'row']) ?>
    <?= Html::beginTag('div', ['class' => 'col-md-12']) ?>
        <?php $form = ActiveForm::begin(['action' => [RouteHelper::ADMIN_DESIGN_MENU_ITEMS_CREATE]]); ?>
            <?= Html::submitButton(Yii::t('app', 'Add'), ['class' => 'btn btn-success col-xs-3 pull-right']) ?>
        <?php ActiveForm::end() ?>
    <?= Html::endTag('div'); ?>
<?= Html::endTag('div'); ?>

<?= Html::beginTag('div', ['class' => 'row margin-top-10']) ?>
    <?= Html::beginTag('div', ['class' => 'col-md-12']) ?>
        <?php if (!empty($items)): ?>
            <?= Sortable::widget([
                'id' => 'menu-items-list',
                'type' => Sortable::TYPE_GRID,
                'items' => $items,
                'pluginEvents' => [
                    'sortupdate' => 'function(event, ui) {
                        $.post(
                            "' . Url::to([RouteHelper::ADMIN_DESIGN_MENU_ITEMS_POSITION]) . '",
                            {old: ui.oldindex, new: ui.item.index()}
                        );
                    }',
                ],
            ]); ?>
        <?php else: ?>
            <?= Html::beginTag('div', ['class' => 'text-center margin-bottom-15']) ?>
                <?= Yii::t(ModuleHelper::ADMIN_DESIGN, 'No menu items available') ?>
            <?= Html::endTag('div'); ?>
        <?php endif ?>
    <?= Html::endTag('div'); ?>
<?= Html::endTag('div'); ?>