<?php
/**
 * This is the template for generating a module class file.
 */

/* @var $this yii\web\View */
/* @var $generator app\modules\gii\generators\module\Generator */

$className = $generator->moduleClass;
$pos = strrpos($className, '\\');
$ns = ltrim(substr($className, 0, $pos), '\\');
$assetClassName = ucfirst(strtolower($generator->moduleID)) . 'Asset';
$parentId = $generator->parentID;
$sourcePath = '@assets/';
if ($parentId) {
    $sourcePath .= $parentId . '/';
    $parentAssetClassName = ucfirst(strtolower($parentId)) . 'Asset';
}
$sourcePath .= $generator->moduleID;

echo "<?php\n";
?>

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: <?= date('d.m.Y H:i') . PHP_EOL ?>
 * via Gii <?= $generator->getName() . PHP_EOL ?>
 */

namespace <?= $generator->getAssetsNamespace() ?>;

use yii\web\AssetBundle;

class <?= $assetClassName ?> extends AssetBundle
{
    public $sourcePath = '<?= $sourcePath ?>';
    public $baseUrl = '@web';
    public $css = [

    ];
    public $js = [

    ];
    public $depends = [
<?php if ($parentId) : ?>
        'app\modules\<?= $parentId ?>\assets\<?= $parentAssetClassName ?>',
<?php else : ?>
        'app\assets\AppAsset',
<?php endif ?>
    ];
}
