<?php

use site\modules\widgets\components\ControlSidebar;
use yii\widgets\Breadcrumbs;

/**
 * @var \yii\web\View $this
 */

?>
<div class="content-wrapper">
    <section class="content-header">
        <?= Breadcrumbs::widget([
            'homeLink' => false,
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
    </section>

    <section class="content">
        <?php if ($this->params['core_module']->id === $this->params['module']->id) : ?>
            <?= $content ?>
        <?php else : ?>
            <?php $this->beginContent($this->params['module']->contentLayout) ?>
                <?= $content ?>
            <?php $this->endContent() ?>
        <?php endif ?>
    </section>
</div>

<?php $this->beginContent('@app/views/engine/footer.php', ['class' => 'main-footer']) ?>
    <div class="col-xs-12 col-sm-offset-1 col-sm-7">
        Copyright &copy; 2013–<?= date('Y') ?> <a href="<?= Yii::$app->params['engine_link'] ?>">Yii2 Community CMS</a>
    </div>

    <div class="col-sm-4 hidden-xs">
        <div class="pull-left">
            <img src="http://domain.ltd/img/gears.png" style="width:20px;margin-bottom:3px" />
            <a href="<?= Yii::$app->params['engine_link'] ?>">
                <?= Yii::$app->params['engine_name'] ?>
            </a>
            <?= Yii::$app->version ?>
        </div>
    </div>
<?php $this->endContent(); ?>

<?= ControlSidebar::widget() ?>
