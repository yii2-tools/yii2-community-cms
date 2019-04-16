<?php

return [
    'admin_email'           => 'support@domain.ltd',

    // engine
    'engine_name'           => 'Yii2 Community CMS',
    'engine_link'           => 'https://github.com/yii2-tools/yii2-community-cms',

    // defaults
    'default_core_module'   => 'site',
    'default_class_session' => 'app\components\web\Session',
    'default_class_cache'   => 'yii\caching\FileCache',
    'default_class_view'    => 'yii\web\View',
    'default_view_path'     => '@app/views',
    'default_error_action'  => 'engine/error',
];
