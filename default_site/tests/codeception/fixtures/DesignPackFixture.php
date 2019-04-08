<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 08.04.16 8:37
 */

namespace tests\codeception\fixtures;

use Yii;
use yii\helpers\FileHelper;
use yii\test\ActiveFixture;
use tests\codeception\_support\traits\MigrationFixtureTrait;

class DesignPackFixture extends ActiveFixture
{
    use MigrationFixtureTrait;

    public $modelClass = 'design\modules\packs\models\DesignPack';
    public $migrations = [
        'db/yii2-community-cms/design/v200000_160328_191459_create_design_packs_table',
        'files/yii2-community-cms/design/v200000_160402_080220_create_custom_design_pack',
    ];

    /**
     * @inheritdoc
     */
    public function load()
    {
        //parent::load();

        $this->reloadMigration();

        $dirPath = Yii::getAlias('@design_packs_dir');
        $dir = array_diff(scandir($dirPath), ['..', '.']);

        foreach ($dir as $elem) {
            $elemPath = $dirPath . DIRECTORY_SEPARATOR . $elem;

            if (is_dir($elemPath) && !in_array($elem, ['default', 'custom'])) {
                FileHelper::removeDirectory($elemPath);
            }
        }
    }
}
