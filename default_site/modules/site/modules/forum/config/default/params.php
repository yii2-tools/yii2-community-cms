<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 19.03.16 12:15
 */

use app\helpers\ModuleHelper;
use yii\tools\params\models\ActiveParam;

return [

    'name' => Yii::t(ModuleHelper::FORUM, 'Forum'),
    'version' => '2.0.1',
    'forum_active' => [
        'class' => 'yii\tools\params\Boolean',
        'description' => Yii::t(ModuleHelper::FORUM, 'Forum active'),
        'value' => true,
    ],
    'forum_sections_count' => [
        'class' => 'yii\tools\params\Number',
        'description' => Yii::t(ModuleHelper::FORUM, 'Sections count'),
        'flags' => ActiveParam::READ_ONLY,
    ],
    'forum_subforums_count' => [
        'class' => 'yii\tools\params\Number',
        'description' => Yii::t(ModuleHelper::FORUM, 'Subforums count'),
        'flags' => ActiveParam::READ_ONLY,
    ],
    'forum_topics_count' => [
        'class' => 'yii\tools\params\Number',
        'description' => Yii::t(ModuleHelper::FORUM, 'Topics count'),
        'flags' => ActiveParam::READ_ONLY,
    ],
    'forum_posts_count' => [
        'class' => 'yii\tools\params\Number',
        'description' => Yii::t(ModuleHelper::FORUM, 'Posts count'),
        'flags' => ActiveParam::READ_ONLY,
    ],
];
