<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 06.02.16 11:36
 */

namespace app\modules\site\assets;

use yii\web\AssetBundle;

class SiteAsset extends AssetBundle
{
    public $sourcePath = '@assets/site';
    public $baseUrl = '@web';
    public $css = [

    ];
    public $js = [

    ];
    public $depends = [
        'app\assets\AppAsset',
    ];
}
