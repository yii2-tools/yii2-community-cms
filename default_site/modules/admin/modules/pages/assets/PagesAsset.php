<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 21.03.2016 16:40
 * via Gii Module Generator
 */

namespace admin\modules\pages\assets;

use yii\web\AssetBundle;

class PagesAsset extends AssetBundle
{
    public $sourcePath = '@assets/admin/pages';
    public $baseUrl = '@web';
    public $css = [

    ];
    public $js = [

    ];
    public $depends = [
        'app\modules\admin\assets\AdminAsset',
    ];
}
