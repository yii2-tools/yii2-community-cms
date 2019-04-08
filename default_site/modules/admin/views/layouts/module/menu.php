<?php

/**
 * @var $this yii\web\View
 */

use yii\bootstrap\Html;
use yii\bootstrap\Nav;

?>

<?= Nav::widget([
    'options' => [
        'class' => 'nav-tabs module-menu-tabs margin-bottom-5',
    ],
    'items' => $this->params['menu']
]);
