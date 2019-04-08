<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 18.05.16 10:25
 */

?>

<div class="row">
    <div class="col-xs-12">
        <strong><?= $model['title'] ?></strong>
    </div>

    <?php if (isset($model['status']) && 0 < intval($model['status'])): ?>
    <div class="col-xs-12">
        <code>
            <?= 'WIDGET_' . strtoupper($model['widget_dir_name']) ?>
        </code>
    </div>
    <?php endif ?>
</div>
