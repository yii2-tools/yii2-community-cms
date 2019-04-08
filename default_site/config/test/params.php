<?php

$__site = require(__DIR__ . DIRECTORY_SEPARATOR . 'yii2_community_cms_site' . DIRECTORY_SEPARATOR . 'site.php');
$__db = require(__DIR__ . DIRECTORY_SEPARATOR . 'yii2_community_cms_site' . DIRECTORY_SEPARATOR . 'db.php');
$__admin = require(__DIR__ . DIRECTORY_SEPARATOR . 'yii2_community_cms_site' . DIRECTORY_SEPARATOR . 'admin.php');

return array_merge([
        // companyName
        'yii2_community_cms_home' => $__yii2_community_cms_home = implode(DIRECTORY_SEPARATOR, ['', 'home', 'companyName']),
        'yii2_community_cms_logs' => $__yii2_community_cms_home . DIRECTORY_SEPARATOR . 'log',
        'yii2_community_cms_libs' => $__yii2_community_cms_libs = ($__yii2_community_cms_home . DIRECTORY_SEPARATOR . 'libs'),
        'yii2_community_cms_libs_pers' => $__yii2_community_cms_libs . DIRECTORY_SEPARATOR . 'projects',
        'yii2_community_cms_domain' => $__yii2_community_cms_domain = 'domain.ltd',
        'yii2_community_cms_static_url' => '//static.' . $__yii2_community_cms_domain,

        // smtp
        'yii2_community_cms_smtp_host' => 'mail.' . $__yii2_community_cms_domain,
        'yii2_community_cms_smtp_user' => 'no-reply@' . $__yii2_community_cms_domain,
        'yii2_community_cms_smtp_pass' => 'ChangeIt',
        'yii2_community_cms_smtp_port' => '25',

        // memcached
        'yii2_community_cms_memcache_host' => '127.0.0.1',
        'yii2_community_cms_memcache_port' => '11211',
        'yii2_community_cms_memcache_size' => '60',

        // redis
        'yii2_community_cms_redis_host' => '127.0.0.1',
        'yii2_community_cms_redis_port' => '6379',
        'yii2_community_cms_redis_db' => $__site['yii2_community_cms_site_id']
    ],
    $__site,
    $__db,
    $__admin
);