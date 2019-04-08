<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 01.04.16 6:48
 *
 * @var \yii\web\View $this
 */

use site\modules\widgets\components\AdminSidebar;

$css = <<<CSS
    @media (min-width: 768px) {
        .sidebar-mini > .wrap > .container {
            padding-left: 65px;
        }
    }
CSS;

$this->registerCss($css);

?>

<?= AdminSidebar::widget() ?>