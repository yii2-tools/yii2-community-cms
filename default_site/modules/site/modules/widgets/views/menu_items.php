<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 03.04.16 3:43
 */

use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\widgets\Menu;
use kartik\sortable\Sortable;
use app\helpers\Html;
use app\helpers\RouteHelper;

if (($user = Yii::$app->getUser()->getIdentity()) && ($isAdmin = $user->isAdmin)) {
    $sortableItems = [];
    $fallbackCount = 0;

    foreach ($items as $item) {
        if (ArrayHelper::getValue($item, 'isFallback', false)) {
            ++$fallbackCount;
        }

        $url = ArrayHelper::getValue($item, 'url');

        if (!is_array($url)) {
            $url = Html::encode($url);
        }

        $sortableItems[] = [
            'content' => Html::a(Html::encode(ArrayHelper::getValue($item, 'label')), $url),
            'options' => array_merge_recursive(
                ArrayHelper::getValue($item, 'options', []),
                [
                    'class' => 'm-item',
                ]
            ),
        ];
    }
}

?>

<?php if (isset($isAdmin) && $isAdmin): ?>
    <?= Sortable::widget([
        'options' => [
            'class' => 'nav navbar-nav hidden-xs',
        ],
        'type' => Sortable::TYPE_GRID,
        'items' => $sortableItems,
        'pluginEvents' => [
            'sortupdate' => 'function(event, ui) {
                $.post(
                    "' . Url::to([RouteHelper::ADMIN_DESIGN_MENU_ITEMS_POSITION]) . '",
                    {old: ui.oldindex - ' . $fallbackCount . ', new: ui.item.index() - ' . $fallbackCount . '}
                );
            }',
        ],
    ]); ?>
<?php endif ?>

<?= Menu::widget([
    'options' => [
        'class' => 'nav navbar-nav' . (isset($isAdmin) && $isAdmin ? ' hidden-sm hidden-md hidden-lg' : ''),
    ],
    'items' => $items,
]) ?>
