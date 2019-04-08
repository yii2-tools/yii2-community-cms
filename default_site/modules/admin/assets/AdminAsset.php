<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 06.02.16 11:37
 */

namespace app\modules\admin\assets;

use yii\web\AssetBundle;

class AdminAsset extends AssetBundle
{
    public $sourcePath = '@assets/admin';
    public $baseUrl = '@web';
    public $css = [
        'css/admin-interface.css',
    ];
    public $js = [

    ];
    public $depends = [
        'app\assets\AppAsset',
    ];
}
