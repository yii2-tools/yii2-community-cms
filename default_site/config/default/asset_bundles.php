<?php
/**
 * This file is generated by the "yii asset" command.
 * DO NOT MODIFY THIS FILE DIRECTLY.
 * @version 2016-05-20 12:45:26
 */
return [
    'all' => [
        'class' => 'yii\\web\\AssetBundle',
        'basePath' => '@webroot/assets',
        'baseUrl' => '@web/assets',
        'js' => [
            'base.js',
        ],
        'css' => [
            'base.css',
        ],
    ],
    'yii\\web\\JqueryAsset' => [
        'sourcePath' => null,
        'js' => [],
        'css' => [],
        'depends' => [
            'all',
        ],
    ],
    'yii\\web\\YiiAsset' => [
        'sourcePath' => null,
        'js' => [],
        'css' => [],
        'depends' => [
            'yii\\web\\JqueryAsset',
            'all',
        ],
    ],
    'yii\\bootstrap\\BootstrapAsset' => [
        'sourcePath' => null,
        'js' => [],
        'css' => [],
        'depends' => [
            'all',
        ],
    ],
    'rmrevin\\yii\\fontawesome\\AssetBundle' => [
        'sourcePath' => null,
        'js' => [],
        'css' => [],
        'depends' => [
            'all',
        ],
    ],
    'yii\\bootstrap\\BootstrapPluginAsset' => [
        'sourcePath' => null,
        'js' => [],
        'css' => [],
        'depends' => [
            'yii\\web\\JqueryAsset',
            'yii\\bootstrap\\BootstrapAsset',
            'all',
        ],
    ],
    'app\\assets\\AdminLteAsset' => [
        'sourcePath' => null,
        'js' => [],
        'css' => [],
        'depends' => [
            'rmrevin\\yii\\fontawesome\\AssetBundle',
            'yii\\web\\YiiAsset',
            'yii\\bootstrap\\BootstrapAsset',
            'yii\\bootstrap\\BootstrapPluginAsset',
            'all',
        ],
    ],
    'app\\assets\\AppAsset' => [
        'sourcePath' => null,
        'js' => [],
        'css' => [],
        'depends' => [
            'yii\\web\\YiiAsset',
            'yii\\bootstrap\\BootstrapAsset',
            'app\\assets\\AdminLteAsset',
            'all',
        ],
    ],
    'app\\modules\\site\\assets\\SiteAsset' => [
        'sourcePath' => null,
        'js' => [],
        'css' => [],
        'depends' => [
            'app\\assets\\AppAsset',
            'all',
        ],
    ],
    'site\\modules\\news\\assets\\NewsAsset' => [
        'sourcePath' => null,
        'js' => [],
        'css' => [],
        'depends' => [
            'app\\modules\\site\\assets\\SiteAsset',
            'all',
        ],
    ],
    'site\\modules\\forum\\assets\\ForumAsset' => [
        'sourcePath' => null,
        'js' => [],
        'css' => [],
        'depends' => [
            'app\\modules\\site\\assets\\SiteAsset',
            'all',
        ],
    ],
    'site\\modules\\pages\\assets\\PagesAsset' => [
        'sourcePath' => null,
        'js' => [],
        'css' => [],
        'depends' => [
            'app\\modules\\site\\assets\\SiteAsset',
            'all',
        ],
    ],
    'site\\modules\\users\\assets\\UsersAsset' => [
        'sourcePath' => null,
        'js' => [],
        'css' => [],
        'depends' => [
            'app\\modules\\site\\assets\\SiteAsset',
            'all',
        ],
    ],
    'app\\modules\\admin\\assets\\AdminAsset' => [
        'sourcePath' => null,
        'js' => [],
        'css' => [],
        'depends' => [
            'app\\assets\\AppAsset',
            'all',
        ],
    ],
    'admin\\modules\\design\\assets\\DesignAsset' => [
        'sourcePath' => null,
        'js' => [],
        'css' => [],
        'depends' => [
            'app\\modules\\admin\\assets\\AdminAsset',
            'all',
        ],
    ],
    'admin\\modules\\design\\modules\\menu\\assets\\MenuAsset' => [
        'sourcePath' => null,
        'js' => [],
        'css' => [],
        'depends' => [
            'admin\\modules\\design\\assets\\DesignAsset',
            'all',
        ],
    ],
    'admin\\modules\\forum\\assets\\ForumAsset' => [
        'sourcePath' => null,
        'js' => [],
        'css' => [],
        'depends' => [
            'app\\modules\\admin\\assets\\AdminAsset',
            'all',
        ],
    ],
    'admin\\modules\\pages\\assets\\PagesAsset' => [
        'sourcePath' => null,
        'js' => [],
        'css' => [],
        'depends' => [
            'app\\modules\\admin\\assets\\AdminAsset',
            'all',
        ],
    ],
    'admin\\modules\\setup\\assets\\SetupAsset' => [
        'sourcePath' => null,
        'js' => [],
        'css' => [],
        'depends' => [
            'app\\modules\\admin\\assets\\AdminAsset',
            'all',
        ],
    ],
    'admin\\modules\\users\\assets\\RbacAsset' => [
        'sourcePath' => null,
        'js' => [],
        'css' => [],
        'depends' => [
            'app\\modules\\admin\\assets\\AdminAsset',
            'all',
        ],
    ],
    'admin\\modules\\widgets\\assets\\WidgetsAsset' => [
        'sourcePath' => null,
        'js' => [],
        'css' => [],
        'depends' => [
            'app\\modules\\admin\\assets\\AdminAsset',
            'all',
        ],
    ],
    'yii\\widgets\\ActiveFormAsset' => [
        'sourcePath' => null,
        'js' => [],
        'css' => [],
        'depends' => [
            'yii\\web\\YiiAsset',
            'all',
        ],
    ],
    'yii\\widgets\\PjaxAsset' => [
        'sourcePath' => null,
        'js' => [],
        'css' => [],
        'depends' => [
            'yii\\web\\YiiAsset',
            'all',
        ],
    ],
    'yii\\grid\\GridViewAsset' => [
        'sourcePath' => null,
        'js' => [],
        'css' => [],
        'depends' => [
            'yii\\web\\YiiAsset',
            'all',
        ],
    ],
    'kartik\\base\\WidgetAsset' => [
        'sourcePath' => null,
        'js' => [],
        'css' => [],
        'depends' => [
            'yii\\web\\JqueryAsset',
            'yii\\bootstrap\\BootstrapAsset',
            'all',
        ],
    ],
    'kartik\\sortable\\SortableAsset' => [
        'sourcePath' => null,
        'js' => [],
        'css' => [],
        'depends' => [
            'yii\\web\\JqueryAsset',
            'yii\\bootstrap\\BootstrapAsset',
            'all',
        ],
    ],
    'kartik\\grid\\GridViewAsset' => [
        'sourcePath' => null,
        'js' => [],
        'css' => [],
        'depends' => [
            'yii\\web\\JqueryAsset',
            'yii\\bootstrap\\BootstrapAsset',
            'all',
        ],
    ],
    'kartik\\grid\\GridResizeColumnsAsset' => [
        'sourcePath' => null,
        'js' => [],
        'css' => [],
        'depends' => [
            'kartik\\grid\\GridViewAsset',
            'all',
        ],
    ],
];