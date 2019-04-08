<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 13.03.2016 08:01
 * via Gii Module Generator
 */

use app\helpers\ModuleHelper;
use yii\tools\params\models\ActiveParam;

return [
    'name' => Yii::t(ModuleHelper::DESIGN, 'Design'),
    'version' => '2.0.1',
    'view_extension' => 'twig',
    'title' => [
        'class' => 'yii\tools\params\String',
        'description' => Yii::t(ModuleHelper::DESIGN, 'Site title'),
    ],
    'copyright' => [
        'class' => 'yii\tools\params\String',
        'description' => Yii::t(ModuleHelper::DESIGN, 'Site copyright'),
    ],
    'design_packs_count' => [
        'class' => 'yii\tools\params\Number',
        'description' => Yii::t(ModuleHelper::DESIGN, 'Design packs count'),
        'flags' => ActiveParam::READ_ONLY,
        'value' => 1,
    ],
];