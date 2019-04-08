<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 27.03.2016 18:59
 * via Gii Module Generator
 */

namespace design\modules\content;

use Yii;
use yii\base\BootstrapInterface;
use yii\caching\Cache;
use yii\helpers\VarDumper;
use app\modules\site\components\Module as BaseModule;
use design\modules\content\interfaces\ResolverInterface;
use design\modules\content\helpers\PlaceholderHelper;
use design\modules\content\models\ActivePlaceholder;

class Module extends BaseModule implements BootstrapInterface
{
    public function init()
    {
        parent::init();

        $this->activatePlaceholders();
    }

    /**
     * @return ResolverInterface
     */
    public function getResolver()
    {
        return $this->get('resolver');
    }

    /**
     * @return Cache
     */
    public function getCache()
    {
        return $this->get('cache');
    }

    /**
     * Activate placeholders if needed
     */
    protected function activatePlaceholders()
    {
        $placeholders = ActivePlaceholder::find()->status(PlaceholderHelper::STATUS_ACTIVATION_REQUIRED)->all();
        Yii::info('Trying to activate placeholders'
            . PHP_EOL . VarDumper::dumpAsString($placeholders, 3), __METHOD__);

        foreach ($placeholders as $placeholder) {
            $placeholder->activate();
        }
    }
}
