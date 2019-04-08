<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 28.03.2016 22:35
 * via Gii Model Generator
 */

namespace design\modules\packs\models;

use Yii;
use yii\helpers\VarDumper;
use yii\helpers\FileHelper;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\tools\interfaces\CompatibleInterface;
use yii\tools\interfaces\BackupInterface;
use yii\tools\behaviors\CountableBehavior;
use site\modules\design\helpers\ModuleHelper;
use design\modules\packs\models\query\DesignPackQuery;

/**
 * This is the model class for table "{{%design_packs}}".
 *
 * @property string $name
 * @property string $title
 * @property string $description
 * @property string $preview
 * @property string $version
 * @property integer $uploaded_at
 * @property integer $updated_at
 */
class DesignPack extends ActiveRecord implements CompatibleInterface, BackupInterface
{
    const CACHE_DEPENDENCY = 'SELECT COUNT(*), MAX([[updated_at]]) FROM [[design_packs]] WHERE [[updated_at]] > 0';

    const CONFIG_FILE_NAME          = 'pack.json';
    const CONFIG_PARAM_NAME         = 'name';
    const CONFIG_PARAM_TITLE        = 'title';
    const CONFIG_PARAM_DESCRIPTION  = 'description';
    const CONFIG_PARAM_PREVIEW      = 'preview';
    const CONFIG_PARAM_VERSION      = 'version';

    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    /**
     * @var \design\modules\packs\Module
     */
    public $module;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%design_packs}}';
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->module = Yii::$app->getModule(ModuleHelper::DESIGN_PACKS);

        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'uploaded_at',
            ],
            'countable' => [
                'class' => CountableBehavior::className(),
                'counterOwner' => $this->module->module,    // Design module.
                'counterParam' => 'design_packs_count',
            ]
        ]);
    }

    public function scenarios()
    {
        return array_merge(parent::scenarios(), [
            static::SCENARIO_CREATE => ['name', 'title', 'version', 'description', 'preview'],
            static::SCENARIO_UPDATE => ['name', 'title', 'version', 'description', 'preview'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'uploaded_at', 'updated_at'], 'required'],
            [['uploaded_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 10],
            [['title', 'description', 'preview'], 'string', 'max' => 255],
            [['version'], 'string', 'max' => 15]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('app', 'Name'),
            'title' => Yii::t('app', 'Title'),
            'description' => Yii::t('app', 'Description'),
            'preview' => Yii::t('app', 'Preview'),
            'version' => Yii::t('app', 'Version'),
            'uploaded_at' => Yii::t('app', 'Uploaded At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function isCompatible()
    {
        // @todo implement versioning (in 2.0.x)
        return true;
    }

    /**
     * @param bool $force
     * @throws \Exception
     */
    public function backup($force = false)
    {
        Yii::trace("Creating backup for design pack '{$this->name}'"
            . ' (force = ' . VarDumper::dumpAsString($force) . ')', __METHOD__);

        $backupDir = $this->getBackupDir();

        if (file_exists($backupDir)) {
            if (!$force) {
                Yii::info("Backup for design pack '{$this->name}' already exists: " . $backupDir, __METHOD__);

                return;
            }

            $this->backupClear();
        }

        $sourceDir = $this->getSourceDir();

        try {
            FileHelper::copyDirectory($sourceDir, $backupDir, ['fileMode' => 0775]);
        } catch (\Exception $e) {
            FileHelper::removeDirectory($backupDir);

            throw $e;
        }
    }

    /**
     * @inheritdoc
     */
    public function backupRestore()
    {
        $sourceDir = $this->getSourceDir();
        $backupDir = $this->getBackupDir();

        Yii::trace("Restoring backup for design pack '{$this->name}'"
            . PHP_EOL . "Backup dir: " . $backupDir
            . PHP_EOL . "Source dir: " . $sourceDir, __METHOD__);

        if (!file_exists($backupDir)) {
            throw new \UnexpectedValueException("Backup for design pack '{$this->name}' doesn't exists");
        }

        FileHelper::removeDirectory($sourceDir);
        FileHelper::copyDirectory($backupDir, $sourceDir);
    }

    /**
     * @inheritdoc
     */
    public function backupClear()
    {
        $backupDir = $this->getBackupDir();

        Yii::trace("Clearing backup for design pack '{$this->name}': " . $backupDir, __METHOD__);

        if (!file_exists($backupDir)) {
            return;
        }

        FileHelper::removeDirectory($backupDir);
    }

    /**
     * Returns path to design pack temporary directory for system actions (e.g. export, backup).
     *
     * @return bool|string path to design pack's tmp directory or false (alias error)
     */
    public function getTmpDir()
    {
        return Yii::getAlias('@design_packs_tmp' . DIRECTORY_SEPARATOR . $this->name);
    }

    /**
     * Returns path to design pack backup files directory.
     *
     * @return bool|string path to design pack's backup files or false (alias error)
     */
    public function getBackupDir()
    {
        return $this->getTmpDir() . DIRECTORY_SEPARATOR . 'source';
    }

    /**
     * Returns path to design pack files directory.
     *
     * @return bool|string path to design pack's files or false (alias error)
     */
    public function getSourceDir()
    {
        return Yii::getAlias('@design_packs_dir' . DIRECTORY_SEPARATOR . $this->name);
    }

    /**
     * @inheritdoc
     * @return DesignPackQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new DesignPackQuery(get_called_class());
    }
}
