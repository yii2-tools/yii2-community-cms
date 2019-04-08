<?php

namespace admin\modules\users\assets;

use yii\web\AssetBundle;

class RbacAsset extends AssetBundle
{
    public $sourcePath = '@assets/admin/users';
    public $baseUrl = '@web';
    public $css = [

    ];
    public $js = [

    ];
    public $depends = [
        'app\modules\admin\assets\AdminAsset',
    ];
}
