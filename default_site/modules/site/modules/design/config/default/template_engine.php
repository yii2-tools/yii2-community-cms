<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 07.03.16 14:40
 *
 * Twig engine configuration.
 *
 * WARNING: Don't remove whole config record, it can be used in code
 * WARNING: Don't implement lexerOptions or extensions configs here
 *  (instead, config some needed extensions in Booting Module)
 */

use app\helpers\SecurityHelper;
use app\modules\site\assets\SiteAsset;
use site\modules\users\models\User;

return [
    'class' => 'site\modules\design\components\ViewRenderer',
    'cachePath' => '@runtime/cache_twig',
    'options' => [
        // @todo flush cache only after edit design pack actions
        'auto_reload' => true,
    ],
    'lexerOptions' => [
        'tag_comment'   => ['{#', '#}'],
        'tag_block'     => ['{%', '%}'],
        'tag_variable'  => ['{{', '}}'],
        'interpolation' => ['#{', '}'],
    ],
    'sandboxOptions' => [
        // http://twig.sensiolabs.org/doc/tags/index.html
        'allowedTags' => [
            'set',
            'spaceless',
            'if',
            'for',
        ],
        // http://twig.sensiolabs.org/doc/filters/index.html
        'allowedFilters' => [
            'escape', 'upper', 'raw', 'merge', 'nl2br', 'length',
        ],
        'allowedMethods' => [
            'yii\web\Application' => [
                'getModule',
            ],
            'yii\web\User' => [
                'getIdentity', 'can',
            ],
            'yii\web\View' => [
                'beginPage', 'endPage',
                'head',
                'beginBody', 'endBody',
                'beginBlock', 'endBlock',
            ],
            'yii\widgets\Block' => [
                '__toString',
            ]
        ],
        'allowedProperties' => [
            'yii\web\Application' => [
                'language', 'charset', 'version',
                'user',
            ],
            'yii\web\User' => [
                'identity',
            ],
            'yii\web\View' => [
                'title', 'blocks',
            ],
            // site/users
            'site\modules\users\Module' => [
                'enableGeneratingPassword',
            ],
            'site\modules\users\models\User' => [
                // fields
                'username', 'email', 'created_at',
                // properties
                'isAdmin', 'roles',
                // relations
                'profile',
            ],
            'site\modules\users\models\Profile' => [
                'name', 'fullName', 'image_url', 'location', 'bio',
            ],
            'site\modules\users\models\Token' => [
                'url',
            ],
            // admin
            'admin\modules\users\components\Role' => [
                'name',
            ],
            // site/forum
            'site\modules\forum\models\Section' => [
                'title', 'slug',
                'subforums',
            ],
            'site\modules\forum\models\Subforum' => [
                'id', 'title', 'slug', 'description', 'topics_num', 'posts_num',
                'section', 'lastPost', 'fixedTopics', 'topicsOnPage',
            ],
            'site\modules\forum\models\Topic' => [
                'id', 'title', 'slug', 'description', 'views_num', 'posts_num',
                'shortTitle',
                'postsCount', 'postsPerPage', 'lastPage',
                'subforum', 'postsOnPage', 'lastPost',
            ],
            'site\modules\forum\models\Post' => [
                'id', 'content', 'is_first',
                'date', 'shortDate', 'editDate', 'shortContent',
                'author', 'editor', 'topic',
            ],
            // site/news
            'site\modules\news\models\NewsRecord' => [
                'id', 'title', 'content', 'slug',
                'date',
                'author', 'editor',
            ],
        ],
        // http://twig.sensiolabs.org/doc/functions/index.html
        'allowedFunctions' => [
            // php
            'md5',
            // custom
            'render', 'style', 'script', 'image',
            // twig
            'set', 'void',
            // yii
            't',
            // yii\helpers\Html
            'csrf', 'a', 'url',
            // app helpers
            'module', 'route', 'skin'
        ],
    ],
    'globals' => [
        // yii
        //'html' => 'yii\helpers\Html',
        //'url' => 'yii\helpers\Url',
        'user' => ($user = \Yii::$app->getUser()->getIdentity()) ? $user : Yii::$container->get(User::className()),
    ],
    'functions' => [
        //php
        'md5' => 'md5',
        // custom
        'render' => new Twig_SimpleFunction('render', function($template, $params = []) {
            return Yii::$app->controller->renderPartial('@templates/' . $template, $params);
        }),
        'style' => new Twig_SimpleFunction('style', function($style) {
            try {
                if (SecurityHelper::validateFilePath($style)) {
                    $filepath = Yii::getAlias('@styles/' . $style);
                    $config = [
                        'extensions' => ['css'],
                        'checkExtensionByMimeType' => false,
                        'mimeTypes' => ['text/plain', 'text/css'],
                    ];
                    if (SecurityHelper::validateFile($filepath, $config)) {
                        $url = Yii::$app->getAssetManager()->publish($filepath)[1];
                        Yii::$app->controller->getView()->registerCssFile($url, ['depends' => [SiteAsset::className()]]);
                    }
                }
            } catch (\Exception $e) {}
        }),
        'script' => new Twig_SimpleFunction('script', function($script) {
            try {
                if (SecurityHelper::validateFilePath($script)) {
                    $filepath = Yii::getAlias('@scripts/' . $script);
                    $config = [
                        'extensions' => ['js'],
                        'checkExtensionByMimeType' => false,
                        'mimeTypes' => ['text/plain', 'text/javascript'],
                    ];
                    if (SecurityHelper::validateFile($filepath, $config)) {
                        $url = Yii::$app->getAssetManager()->publish($filepath)[1];
                        Yii::$app->controller->getView()->registerJsFile($url, ['depends' => [SiteAsset::className()]]);
                    }
                }
            } catch (\Exception $e) {}
        }),
        'image' => new Twig_SimpleFunction('image', function($image) {
            try {
                if (SecurityHelper::validateFilePath($image)) {
                    $filepath = Yii::getAlias('@images/' . $image);
                    $config = [
                        'extensions' => ['png', 'jpeg', 'jpg', 'gif', 'bmp', 'ico'],
                        'checkExtensionByMimeType' => false,
                        'mimeTypes' => [
                            'image/png', 'image/jpeg', 'image/pjpeg', 'image/gif',
                            'image/bmp', 'image/x-bmp', 'image/x-ms-bmp',
                            'image/x-icon', 'image/vnd.microsoft.icon',
                        ],
                    ];
                    if (SecurityHelper::validateFile($filepath, $config)) {
                        return Yii::$app->getAssetManager()->publish($filepath)[1];
                    }
                }
            } catch (\Exception $e) {}
        }),
        // yii
        't' => 'Yii::t',
        // yii\helpers\Html
        'csrf' => 'yii\helpers\Html::csrfMetaTags',
        'a' => 'yii\helpers\Html::a',
        'url' => 'yii\helpers\Url::to',
        // app helpers
        'module' => 'app\helpers\ModuleHelper::constant',
        'route' => 'app\helpers\RouteHelper::constant',
    ],
    'filters' => [

    ],
    'uses' => [
        //'yii\bootstrap',
        //'yii\widgets',
    ],
];
