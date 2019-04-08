<?php

/**
 * Configuration file for the "yii asset" console command.
 */

Yii::setAlias('@webroot', __DIR__ . '/../../web');
Yii::setAlias('@web', '/');
Yii::setAlias('@assets', '@app/assets/dist');

return [
    // Adjust command/callback for JavaScript files compressing:
    'jsCompressor' => 'java -jar default_site/vendor/bin/compiler.jar --js {from} --js_output_file {to} -W QUIET',
    // Adjust command/callback for CSS files compressing:
    'cssCompressor' => 'java -jar default_site/vendor/bin/yuicompressor.jar --type css {from} -o {to}',
    // The list of asset bundles to compress:
    'bundles' => [
        'app\assets\AppAsset',
        'app\assets\AdminLteAsset',
        // site
        'app\modules\site\assets\SiteAsset',
        'site\modules\news\assets\NewsAsset',
        'site\modules\forum\assets\ForumAsset',
        'site\modules\pages\assets\PagesAsset',
        //'site\modules\plugins\assets\PluginsAsset',   // problems with TinyMCE asset
        'site\modules\users\assets\UsersAsset',
        // admin
        'app\modules\admin\assets\AdminAsset',
        'admin\modules\design\assets\DesignAsset',
        'admin\modules\design\modules\menu\assets\MenuAsset',
        'admin\modules\forum\assets\ForumAsset',
        'admin\modules\pages\assets\PagesAsset',
        'admin\modules\plugins\assets\PluginsAsset',
        'admin\modules\setup\assets\SetupAsset',
        'admin\modules\users\assets\RbacAsset',
        'admin\modules\widgets\assets\WidgetsAsset',
        // vendor
        'yii\widgets\ActiveFormAsset',
        'yii\widgets\PjaxAsset',
        'yii\grid\GridViewAsset',
        'kartik\base\WidgetAsset',
        'kartik\sortable\SortableAsset',
        'kartik\grid\GridViewAsset',
        'kartik\grid\GridResizeColumnsAsset',

        // @todo check this (will be since 2.0.1), lang files should be valid after migration to static domain!
        'dosamigos\ckeditor\CKEditorWidgetAsset',
        'dosamigos\ckeditor\CKEditorAsset',
    ],
    // Asset bundle for compression output:
    'targets' => [
        'all' => [
            'class' => 'yii\web\AssetBundle',
            'basePath' => '@webroot/assets',
            'baseUrl' => '@web/assets',
            'js' => 'base.js',
            'css' => 'base.css',
        ],
    ],
    // Asset manager configuration:
    'assetManager' => [
        'basePath' => '@webroot/assets',
        'baseUrl' => '@web/assets',
    ],
];