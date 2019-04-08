<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 24.04.16 14:41
 */

use app\helpers\ModuleHelper;

$this->title = Yii::t(ModuleHelper::ADMIN_PAGES, 'Create new page');
$this->params['breadcrumbs'][] = $this->title;

?>

<?= $this->render('_form', ['model' => $model]);
