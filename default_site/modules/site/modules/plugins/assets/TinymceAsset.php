<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 18.04.16 22:22
 */

namespace site\modules\plugins\assets;

use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\helpers\Url;
use yii\web\AssetBundle;
use Yii;
use yii\web\AssetManager;

/**
 * Asset of editor for plugins.
 * Port from engine 1.0
 *
 * @package site\modules\plugins\assets
 */
class TinymceAsset extends AssetBundle
{
    public $sourcePath = '@vendor/tinymce/tinymce';
    public $css = [

    ];
    public $js = [
        'tinymce.min.js',
        'langs/ru.js',
    ];
    public $depends = [

    ];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $languagesDir = Yii::getAlias($this->sourcePath) . DIRECTORY_SEPARATOR . 'langs';

        if (!file_exists($languagesDir)) {
            FileHelper::copyDirectory(Yii::getAlias('@assets/site/plugins/js/tinymce/langs'), $languagesDir);
        }
    }
}
