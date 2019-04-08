<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 18.05.16 16:02
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Class ExternalAsset
 * @package app\assets
 */
class ExternalAsset extends AssetBundle
{
    public $sourcePath = '@assets';
    public $baseUrl = '@web';
    public $css = [

    ];
    public $js = [
        'https://cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js',
        '//cdn.jsdelivr.net/jdenticon/1.3.2/jdenticon.min.js',
    ];
    public $depends = [
        'app\assets\AppAsset',
    ];
}
