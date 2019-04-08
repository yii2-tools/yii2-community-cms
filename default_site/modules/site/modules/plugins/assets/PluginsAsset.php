<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 19.03.2016 13:43
 * via Gii Module Generator
 */

namespace site\modules\plugins\assets;

use yii\web\AssetBundle;

class PluginsAsset extends AssetBundle
{
    public $sourcePath = '@assets/site/plugins';
    public $baseUrl = '@web';
    public $css = [

    ];
    public $js = [
        'js/php.js',
    ];
    public $depends = [
        'app\modules\site\assets\SiteAsset',
        'site\modules\plugins\assets\TinymceAsset',
    ];
}
