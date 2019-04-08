<?php
/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\module\Generator */

$assetsNamespace = $generator->getAssetsNamespace();
$assetClassName = ucfirst(strtolower($generator->moduleID)) . 'Asset';

echo "<?php\n";
?>

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: <?= date('d.m.Y H:i') . PHP_EOL ?>
 * via Gii <?= $generator->getName() . PHP_EOL ?>
 */

use yii\bootstrap\Html;
use app\helpers\ModuleHelper;
use <?= $assetsNamespace ?>\<?= $assetClassName ?>;

<?= $assetClassName ?>::register($this);
?>

<?= '<?=' ?> $this->render('@app/views/_alert', [
<?php if (isset($generator->parentID)) : ?>
    'module' => Yii::$app->getModule(ModuleHelper::<?= strtoupper($generator->parentID) ?>_<?= strtoupper($generator->moduleID) ?>),
<?php else : ?>
    'module' => Yii::$app->getModule(ModuleHelper::<?= strtoupper($generator->moduleID) ?>),
<?php endif ?>
]) ?>

<?= "<?= ''/*" ?> $this->render('/_menu')*/ ?>

<?= '<?=' ?> Html::beginTag('div', ['class' => 'row']) ?>
    <?= '<?=' ?> Html::beginTag('div', ['class' => 'col-md-12']) ?>
        <?= '<?=' ?> $content ?>
    <?= '<?=' ?> Html::endTag('div') ?>
<?= '<?=' ?> Html::endTag('div') ?>
