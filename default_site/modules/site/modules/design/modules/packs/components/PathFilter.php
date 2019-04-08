<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 05.04.16 10:09
 */

namespace design\modules\packs\components;

use Yii;
use yii\tools\components\PathFilter as BasePathFilter;
use site\modules\design\helpers\ModuleHelper;

class PathFilter extends BasePathFilter
{
    /**
     * Returns array of file names which should be excluded from each design pack (force exclude).
     *
     * @return array except file names
     */
    public function getExceptFileNames()
    {
        // here can be some runtime allow-path logic.

        return Yii::$app->getModule(ModuleHelper::DESIGN_PACKS)->params['design_packs_content_except'];
    }

    /**
     * @inheritdoc
     */
    public function filter(array $fileNames, array $except = [], $reverseResult = false)
    {
        if (empty($except)) {
            $except = $this->getExceptFileNames();
        }

        return parent::filter($fileNames, $except, $reverseResult);
    }
}
