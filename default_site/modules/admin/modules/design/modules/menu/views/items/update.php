<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 10.04.16 13:16
 */

use site\modules\design\helpers\ModuleHelper;

$this->title = Yii::t(ModuleHelper::ADMIN_DESIGN, 'Update menu item');
$this->params['breadcrumbs'][] = $this->title;

?>

<?= $this->render('/items/_form', ['model' => $model]);
