<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 03.04.16 11:47
 */

namespace app\assets;

use dmstr\web\AdminLteAsset as BaseAdminLteAsset;

class AdminLteAsset extends BaseAdminLteAsset
{
    // use app-slte-skins.css (AppAsset) instead (bugfix)
    public $skin = false;
}
