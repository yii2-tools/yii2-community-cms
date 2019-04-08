<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 18.03.2016 19:55
 * via Gii Module Generator
 */

namespace ci18n\modules\packs;

use Yii;
use yii\base\BootstrapInterface;
use yii\i18n\PhpMessageSource;
use app\helpers\ModuleHelper;
use app\components\Module as BaseModule;

class Module extends BaseModule implements BootstrapInterface
{
    public function bootstrap($app)
    {
        parent::bootstrap($app);

        Yii::$app->get('i18n')->translations['app'] = [
            'class'    => PhpMessageSource::className(),
            'basePath' => '@ci18n/modules/packs/source',
        ];

        Yii::$app->get('i18n')->translations['*'] = [
            'class'    => PhpMessageSource::className(),
            'basePath' => '@ci18n/modules/packs/source',
        ];

        // fix for migrations. i18n for site should be initialized early.
        Yii::$app->get('i18n')->translations[ModuleHelper::SITE . '*'] = [
            'class'    => PhpMessageSource::className(),
            'basePath' => '@i18n/modules/packs/source',
        ];
    }
}
