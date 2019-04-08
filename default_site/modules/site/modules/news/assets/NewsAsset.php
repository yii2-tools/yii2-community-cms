<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 08.05.2016 00:12
 * via Gii Module Generator
 */

namespace site\modules\news\assets;

use yii\web\AssetBundle;

class NewsAsset extends AssetBundle
{
    public $sourcePath = '@assets/site/news';
    public $baseUrl = '@web';
    public $css = [
        'css/news.css',
    ];
    public $js = [

    ];
    public $depends = [
        'app\modules\site\assets\SiteAsset',
    ];
}
