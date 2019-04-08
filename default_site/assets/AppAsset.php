<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

class AppAsset extends AssetBundle
{
    public $sourcePath = '@assets';
    public $baseUrl = '@web';
    public $css = [
        'css/app.css',
        'css/app-menu.css',
        'css/app-alte.css',         // fixes AdminLTE
        'css/app-alte-skins.css',
        'css/app-interface.css',
        'css/app-pace.css',
        'css/app-hacks.css',
    ];
    public $js = [
        'js/pace-config.js',
        'js/admin-alte.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'app\assets\AdminLteAsset',
    ];
}
