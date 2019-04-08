<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\bootstrap\Html;
use dmstr\helpers\AdminLteHelper;
use app\helpers\ModuleHelper;
use app\assets\ExternalAsset;
use app\modules\admin\assets\AdminAsset;

AdminAsset::register($this);
ExternalAsset::register($this);

// @todo remove
$this->params['alte_dir'] = Yii::$app->assetManager->getPublishedUrl('@vendor/almasaeed2010/adminlte/dist');

$this->params['is_setup_context'] = $this->context->module->id === ModuleHelper::id(ModuleHelper::ADMIN_SETUP);
$this->params['user'] = Yii::$app->getUser()->getIdentity();
$this->params['profile'] = $this->params['user']->profile;
$this->params['sidebar_collapse'] = intval($this->params['user']->params['sidebar_collapse']) ? ' sidebar-collapse' : '';
$this->params['core_module'] = Yii::$app->getModule(ModuleHelper::ADMIN);
$this->params['sub_modules'] = $this->params['core_module']->getModules(false, true);
$this->params['module'] = $this->params['is_setup_context'] ? $this->context->action->params['module'] : $this->context->module;

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-pjax-version" content="<?= Yii::$app->version ?>">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="hold-transition skin-black sidebar-mini<?= $this->params['sidebar_collapse'] ?>">
<?php $this->beginBody() ?>

<div class="wrapper">
    <?= $this->render('@admin/views/layouts/header') ?>
    <?= $this->render('@admin/views/layouts/sidebar') ?>
    <?= $this->render('@admin/views/layouts/content', ['content' => $content]) ?>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>