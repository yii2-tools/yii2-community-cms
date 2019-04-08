<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 09.04.16 15:57
 */

namespace admin\modules\design\modules\menu\assets;

use yii\web\AssetBundle;

/**
 * Class MenuAsset
 * @package admin\modules\design\modules\menu\assets
 */
class MenuAsset extends AssetBundle
{
    public $sourcePath = '@assets/admin/design/menu';
    public $baseUrl = '@web';
    public $css = [
        'css/menu.css',
    ];
    public $js = [

    ];
    public $depends = [
        'admin\modules\design\assets\DesignAsset',
    ];
}
