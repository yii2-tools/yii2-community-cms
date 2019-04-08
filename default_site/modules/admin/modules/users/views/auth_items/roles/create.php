<?php

/**
 * @var $model admin\modules\users\models\Role
 * @var $this  yii\web\View
 */

use app\helpers\ModuleHelper;

$this->title = Yii::t(ModuleHelper::ADMIN_USERS, 'Create new role');
$this->params['breadcrumbs'][] = $this->title;

?>

<?= $this->render('/auth_items/_action', ['model' => $model]);
