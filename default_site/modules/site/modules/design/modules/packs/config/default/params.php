<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 28.03.2016 21:49
 * via Gii Module Generator
 */

use site\modules\design\helpers\ModuleHelper;

return [
    'name' => 'Design Packs',
    'version' => '2.0.0',
    'design_pack' => [
        'class' => 'design\modules\packs\models\DesignPackParam',
        'description' => Yii::t(ModuleHelper::DESIGN_PACKS, 'Active design pack'),
        'value' => 'custom'
    ],
    'design_packs_limit' => 10,
    'design_packs_name_reserved' => ['default'],
    'design_packs_name_pattern' => '/^[a-zA-Z0-9_]{3,10}$/',
    'design_packs_dir' => '@design/modules/packs/source',
    'design_packs_tmp' => '@runtime/design/packs',
    'design_packs_format' => [
        'rar', 'zip',
        'tar', 'tar.gz', 'tar.bz2'
    ],
    'design_packs_mime_types' => [
        'application/x-rar', 'application/x-rar-compressed',
        'application/zip', 'application/x-tar', 'application/x-gzip', 'application/x-bzip2',
    ],
    'design_packs_size_max' => '2097152',   // bytes
    // Not for direct using. Use component 'pathFilter' ('design/packs' module) instead
    'design_packs_content_except' => [
        '__',
        '.DS_Store',
        '.git',
        '.db',
    ],
    'design_packs_content_format' => [
        'json', 'twig', 'css', 'js',
        'png', 'jpeg', 'jpg', 'gif', 'bmp', 'ico',
    ],
    'design_packs_preview_format' => [
        'png', 'jpeg', 'jpg', 'bmp',
    ],
    'design_packs_content_mime_types' => [
        'application/json',
        'text/plain', 'text/html', 'text/javascript', 'text/css',
        'image/png',
        'image/jpeg', 'image/pjpeg',
        'image/gif',
        'image/bmp', 'image/x-bmp', 'image/x-ms-bmp',
        'image/x-icon', 'image/vnd.microsoft.icon',
    ],
    'design_packs_content_size_max' => '524288',   // bytes
    'design_packs_preview_size_max' => '65536',    // bytes
];
