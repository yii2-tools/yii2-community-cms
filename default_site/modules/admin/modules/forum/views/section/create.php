<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 06.05.16 19:35
 */

use app\helpers\ModuleHelper;

$this->title = Yii::t(ModuleHelper::ADMIN_FORUM, 'Create new section');
$this->params['breadcrumbs'][] = $this->title;

?>

<?= $this->render('_form', ['model' => $model]);