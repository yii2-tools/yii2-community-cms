<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 21.03.2016 16:41
 * via Gii Module Generator
 */

namespace admin\modules\widgets\assets;

use yii\web\AssetBundle;

class WidgetsAsset extends AssetBundle
{
    public $sourcePath = '@assets/admin/widgets';
    public $baseUrl = '@web';
    public $css = [
        'css/admin-widgets.css',
    ];
    public $js = [

    ];
    public $depends = [
        'app\modules\admin\assets\AdminAsset',
    ];
}
