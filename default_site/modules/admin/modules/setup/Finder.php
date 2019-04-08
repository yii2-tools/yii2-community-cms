<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 14.02.16 1:48
 */

namespace admin\modules\setup;

use Yii;
use yii\base\InvalidCallException;
use yii\base\NotSupportedException;
use yii\base\Object;
use app\helpers\ModuleHelper;
use yii\tools\params\ActiveParams;

/**
 * Class Finder
 */
class Finder extends Object
{
    /**
     * @param $key
     * @param $value
     * @param bool $multiple
     * @return $this
     * @throws \yii\base\NotSupportedException
     */
    public function findModel($key, $value, $multiple = true)
    {
        if ($key == 'category') {
            return $this->findByModule(preg_replace('/.*\//', '', $value), $multiple);
        }

        throw new NotSupportedException();
    }

    /**
     * Notes:
     * – Finder of Setup module find NOT READ-ONLY params (for module self and submodules)
     *
     * @param $value
     * @return array
     * @throws \yii\base\InvalidCallException
     */
    public function findByModule($value)
    {
        $module = Yii::$app->getModule(ModuleHelper::SITE . '/' . $value);

        if (is_null($module)) {
            return [];
        }

        if (!$module->params instanceof ActiveParams) {
            // see app\components\Module::init()
            throw new InvalidCallException("Module must have ActiveParams instance in 'params' property value");
        }

        $result = $this->getActiveParams($module);
        $submodules = $module->getModules(false, true);

        foreach ($submodules as $submodule) {
            $result = array_merge($result, $this->getActiveParams($submodule));
        }

        return $result;
    }

    /**
     * @param $module
     * @return array
     */
    protected function getActiveParams($module)
    {
        $models = $module->params->toArray();
        $result = [];

        foreach ($models as $model) {
            if (!$model->isReadOnly()) {
                $result[] = $model;
            }
        }

        return $result;
    }
}
