<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Url;
use yii\bootstrap\NavBar;
use app\helpers\Html;
use app\assets\AppAsset;

AppAsset::register($this);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?= NavBar::widget([
        'brandLabel' => Html::faIcon('arrow-left') . ' ' . Yii::t('app', 'Go back'),
        'brandUrl' => Url::previous(),
        'options' => [
            'class' => 'navbar-fixed-top navbar-engine',
        ],
    ]); ?>

    <div class="container">
        <?= $content ?>
    </div>
</div>

<?= $this->render('@app/views/engine/footer'); ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
