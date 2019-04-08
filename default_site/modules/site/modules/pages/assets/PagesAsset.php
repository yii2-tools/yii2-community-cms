<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 21.03.2016 16:49
 * via Gii Module Generator
 */

namespace site\modules\pages\assets;

use yii\web\AssetBundle;

class PagesAsset extends AssetBundle
{
    public $sourcePath = '@assets/site/pages';
    public $baseUrl = '@web';
    public $css = [

    ];
    public $js = [

    ];
    public $depends = [
        'app\modules\site\assets\SiteAsset',
    ];
}
