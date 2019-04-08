<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 21.03.2016 16:50
 * via Gii Module Generator
 */

namespace site\modules\forum\assets;

use yii\web\AssetBundle;

class ForumAsset extends AssetBundle
{
    public $sourcePath = '@assets/site/forum';
    public $baseUrl = '@web';
    public $css = [
        'css/forum.css',
        'css/stats.css',
        'css/online.css',
    ];
    public $js = [

    ];
    public $depends = [
        'app\modules\site\assets\SiteAsset',
    ];
}
