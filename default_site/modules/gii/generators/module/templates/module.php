<?php
/**
 * This is the template for generating a module class file.
 */

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\module\Generator */

$className = $generator->moduleClass;
$pos = strrpos($className, '\\');
$ns = ltrim(substr($className, 0, $pos), '\\');
$className = substr($className, $pos + 1);

echo "<?php\n";
?>

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: <?= date('d.m.Y H:i') . PHP_EOL ?>
 * via Gii <?= $generator->getName() . PHP_EOL ?>
 */

namespace <?= $ns ?>;

use app\components\Module as BaseModule;

class <?= $className ?> extends BaseModule
{

}
