<?php

namespace site\modules\users\assets;

use yii\web\AssetBundle;

class UsersAsset extends AssetBundle
{
    public $sourcePath = '@assets/site/users';
    public $baseUrl = '@web';
    public $css = [

    ];
    public $js = [

    ];
    public $depends = [
        'app\modules\site\assets\SiteAsset',
    ];
}
