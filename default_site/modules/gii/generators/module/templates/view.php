<?php
/* @var \yii\web\View $this */
/* @var \app\modules\gii\generators\module\Generator $generator */

echo "<?php\n";
?>

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: <?= date('d.m.Y H:i') . PHP_EOL ?>
 * via Gii <?= $generator->getName() . PHP_EOL ?>
 */

//$this->title = Yii::t('<?= $generator->moduleID ?>', '<?= $generator->moduleName ?>');
?>

<?php if (isset($generator->parentID)) : ?>
<?= '<?php' ?> $this->beginContent('@<?= $generator->parentID ?>/modules/<?= $generator->moduleID ?>/views/layouts/module.php') ?>
<?php endif ?>
<div class="<?= $generator->moduleID . '-default-index' ?>">
    <h1><?= "<?= " ?>$this->context->action->uniqueId ?></h1>
    <p>
        This is the view content for action "<?= "<?= " ?>$this->context->action->id ?>".
        The action belongs to the controller "<?= "<?= " ?>get_class($this->context) ?>"
        in the "<?= "<?= " ?>$this->context->module->id ?>" module.
    </p>
    <p>
        You may customize this page by editing the following file:<br>
        <code><?= "<?= " ?>__FILE__ ?></code>
    </p>
</div>
<?php if (isset($generator->parentID)) : ?>
<?= '<?php' ?> $this->endContent() ?>
<?php endif ?>