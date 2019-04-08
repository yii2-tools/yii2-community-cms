<?php

/**
 * @var $models[0] admin\modules\users\models\Role
 * @var $this  yii\web\View
 */

use app\helpers\ModuleHelper;

$this->title = Yii::t(ModuleHelper::ADMIN_USERS, 'Create new permission');
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('/auth_items/_action', ['model' => $model]);
