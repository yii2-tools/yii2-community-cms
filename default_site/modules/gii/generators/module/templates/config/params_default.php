<?php
/**
 * This is the template for generating a module class file.
 */

/* @var $this yii\web\View */
/* @var $generator app\modules\gii\generators\module\Generator */

$className = $generator->moduleClass;
$pos = strrpos($className, '\\');
$ns = ltrim(substr($className, 0, $pos), '\\');
$className = substr($className, $pos + 1);
$moduleName = $generator->moduleName;

echo "<?php\n";
?>

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: <?= date('d.m.Y H:i') . PHP_EOL ?>
 * via Gii <?= $generator->getName() . PHP_EOL ?>
 */

return [
    'name' => '<?= $moduleName ?>',
    'version' => '2.0.0',
];