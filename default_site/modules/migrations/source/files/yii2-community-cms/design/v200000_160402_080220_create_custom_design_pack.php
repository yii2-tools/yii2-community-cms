<?php

use yii\db\Migration;
use yii\helpers\FileHelper;
use site\modules\design\helpers\ModuleHelper;
use design\modules\packs\models\DesignPack;

class v200000_160402_080220_create_custom_design_pack extends Migration
{
    public function up()
    {
        $sourceDir = Yii::getAlias('@design_packs_dir/default');
        $destDir = Yii::getAlias('@design_packs_dir/custom');
        FileHelper::copyDirectory($sourceDir, $destDir, ['fileMode' => 0775]);

        $config = <<<CONFIG
{
	"name": "custom",
	"title": "{title}",
	"description": "{description}",
	"preview": "images/preview.png",
	"version": "{version}"
}
CONFIG;

        $config = Yii::t('app', $config, [
            'title' => Yii::t(ModuleHelper::DESIGN_PACKS, 'Design pack for my site'),
            'description' => Yii::t(ModuleHelper::DESIGN_PACKS, 'This is pre-installed design pack. You can freely modify its contents or download and activate another one. Activated design pack cannot be removed until you install a replacement'),
            'version' => Yii::$app->version,
        ]);

        file_put_contents($destDir . DIRECTORY_SEPARATOR . DesignPack::CONFIG_FILE_NAME, $config);
    }

    public function down()
    {
        $dir = Yii::getAlias('@design_packs_dir/custom');
        FileHelper::removeDirectory($dir);
        FileHelper::createDirectory($dir);
        file_put_contents($dir . DIRECTORY_SEPARATOR . '.gitignore', '*' . PHP_EOL . '!.gitignore');
    }
}
