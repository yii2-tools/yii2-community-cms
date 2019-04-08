<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 08.02.2016 14:19
 * via Gii Module Generator
 */

namespace admin\modules\setup\assets;

use yii\web\AssetBundle;

class SetupAsset extends AssetBundle
{
    public $sourcePath = '@assets/admin/setup';
    public $baseUrl = '@web';
    public $css = [

    ];
    public $js = [

    ];
    public $depends = [
        'app\modules\admin\assets\AdminAsset',
    ];
}
