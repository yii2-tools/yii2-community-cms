<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 21.03.2016 16:39
 * via Gii Module Generator
 */

namespace admin\modules\design\assets;

use yii\web\AssetBundle;

class DesignAsset extends AssetBundle
{
    public $sourcePath = '@assets/admin/design';
    public $baseUrl = '@web';
    public $css = [

    ];
    public $js = [

    ];
    public $depends = [
        'app\modules\admin\assets\AdminAsset',
    ];
}
