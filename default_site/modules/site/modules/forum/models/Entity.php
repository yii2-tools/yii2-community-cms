<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 30.04.16 11:07
 */

namespace site\modules\forum\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use app\helpers\ModuleHelper;
use yii\tools\secure\ActiveRecord;
use site\modules\forum\Module;

/**
 * This is the base model class for forum entities.
 */
abstract class Entity extends ActiveRecord
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';
    const SCENARIO_DELETE = 'delete';

    /**
     * @var Module
     */
    public $module;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->module = Yii::$app->getModule(ModuleHelper::FORUM);

        parent::init();
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return array_merge(
            [
                'timestamp' => [
                    'class' => TimestampBehavior::className(),
                ],
            ],
            parent::behaviors()
        );
    }

    /**
     * @inheritdoc
     */
    public function transactions()
    {
        return [
            static::SCENARIO_DEFAULT => static::OP_ALL,
            static::SCENARIO_CREATE => static::OP_ALL,
            static::SCENARIO_UPDATE => static::OP_ALL,
            static::SCENARIO_DELETE => static::OP_ALL,
        ];
    }
}
