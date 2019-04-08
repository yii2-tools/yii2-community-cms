<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 17.03.16 18:28
 */

namespace tests\codeception\fixtures;

use Yii;
use yii\helpers\FileHelper;
use yii\test\Fixture;

class AssetFixture extends Fixture
{
    public function load()
    {
        parent::load();

        $assetsDir = Yii::getAlias(Yii::$app->getAssetManager()->basePath);
        if (file_exists($assetsDir)) {
            FileHelper::removeDirectory($assetsDir);
        }
        FileHelper::createDirectory($assetsDir);
        file_put_contents($assetsDir . DIRECTORY_SEPARATOR . '.gitkeep', '');
    }
}
