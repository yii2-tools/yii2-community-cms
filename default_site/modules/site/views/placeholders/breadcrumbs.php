<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 01.04.16 4:46
 */

use yii\widgets\Breadcrumbs;

?>

<?= Breadcrumbs::widget([
    'homeLink' => false,
    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
]) ?>