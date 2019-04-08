<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 22.02.2016 14:13
 * via Gii Module Generator
 */

namespace admin\modules\plugins\assets;

use yii\web\AssetBundle;

class PluginsAsset extends AssetBundle
{
    public $sourcePath = '@assets/admin/plugins';
    public $baseUrl = '@web';
    public $css = [
        'css/admin-plugins.css',
    ];
    public $js = [

    ];
    public $depends = [
        'app\modules\admin\assets\AdminAsset',
    ];
}
