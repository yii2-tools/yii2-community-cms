<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\modules\gii\generators\module;

use app\helpers\ModuleHelper;
use Yii;
use yii\base\Model;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\gii\CodeFile;

/**
 * This generator will generate the skeleton code needed by a module.
 *
 * @property string $controllerNamespace The controller namespace of the module. This property is read-only.
 * @property boolean $modulePath The directory that contains the module class. This property is read-only.
 */
class Generator extends \yii\gii\Generator
{
    public $moduleClass;
    public $moduleName;
    public $moduleID;
    public $parentID;

    public function init()
    {
        parent::init();

        Yii::$app->loader->bootstrap(Yii::$app->getModule(ModuleHelper::SITE));
        Yii::$app->loader->bootstrap(Yii::$app->getModule(ModuleHelper::ADMIN));
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Module Generator';
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return 'This generator helps you to generate the skeleton code needed by a Yii module.';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['moduleID', 'parentID', 'moduleName', 'moduleClass'], 'filter', 'filter' => 'trim'],
            [['moduleID', 'moduleClass'], 'required'],
            [['moduleID', 'parentID'], 'match', 'pattern' => '/^[\w\\-]+$/', 'message' => 'Only word characters and dashes are allowed.'],
            [['moduleClass'], 'match', 'pattern' => '/^[\w\\\\]*$/', 'message' => 'Only word characters and backslashes are allowed.'],
            [['moduleClass'], 'validateModuleClass'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'moduleID' => 'Module ID',
            'parentID' => 'Parent Module ID',
            'moduleName' => 'Module Name',
            'moduleClass' => 'Module Class',
        ];
    }

    /**
     * @inheritdoc
     */
    public function hints()
    {
        return [
            'moduleID' => 'This refers to the ID of the module, e.g., <code>users</code>.',
            'parentID' => 'This refers to the ID of the parent module, e.g., <code>site</code>.',
            'moduleName' => 'This is the name of the module, e.g., <code>Users Management</code>.',
            'moduleClass' => 'This is the fully qualified class name of the module, e.g., <code>site\modules\users\Module</code>.',
        ];
    }

    /**
     * @inheritdoc
     */
    public function successMessage()
    {
        if (Yii::$app->hasModule($this->moduleID)) {
            $link = Html::a('try it now', Yii::$app->getUrlManager()->createUrl($this->moduleID), ['target' => '_blank']);

            return "The module has been generated successfully. You may $link.";
        }

        $output = <<<EOD
<p>The module has been generated successfully.</p>
<p>To access the module, you need to add this to your application configuration:</p>
EOD;
        $code = <<<EOD
<?php
    ......
    'modules' => [
        '{$this->moduleID}' => [
            'class' => '{$this->moduleClass}',
        ],
    ],
    ......
EOD;

        return $output . '<pre>' . highlight_string($code, true) . '</pre>';
    }

    /**
     * @inheritdoc
     */
    public function requiredTemplates()
    {
        return ['module.php', 'controller.php', 'view.php'];
    }

    /**
     * @inheritdoc
     */
    public function generate()
    {
        $files = [];
        $modulePath = $this->getModulePath();
        $files[] = new CodeFile(
            $modulePath . '/' . StringHelper::basename($this->moduleClass) . '.php',
            $this->render("module.php")
        );
        $files[] = new CodeFile(
            $modulePath . '/controllers/DefaultController.php',
            $this->render("controller.php")
        );
        $files[] = new CodeFile(
            $modulePath . '/views/default/index.php',
            $this->render("view.php")
        );
        if ($this->template === 'default') {
            return $files;
        }
        $files[] = new CodeFile(
            $modulePath . '/views/layouts/module.php',
            $this->render("layout.php")
        );
        $files[] = new CodeFile(
            $modulePath . '/views/_menu.php',
            $this->render("_menu.php")
        );
        // config
        $files[] = new CodeFile(
            $modulePath . '/config/default/module.php',
            $this->render("/config/module_default.php")
        );
        $files[] = new CodeFile(
            $modulePath . '/config/default/params.php',
            $this->render("/config/params_default.php")
        );
        $files[] = new CodeFile(
            $modulePath . '/config/dev/module.php',
            $this->render("/config/module.php")
        );
        $files[] = new CodeFile(
            $modulePath . '/config/dev/params.php',
            $this->render("/config/params.php")
        );
        $files[] = new CodeFile(
            $modulePath . '/config/test/module.php',
            $this->render("/config/module.php")
        );
        $files[] = new CodeFile(
            $modulePath . '/config/test/params.php',
            $this->render("/config/params.php")
        );
        $files[] = new CodeFile(
            $modulePath . '/config/prod/module.php',
            $this->render("/config/module.php")
        );
        $files[] = new CodeFile(
            $modulePath . '/config/prod/params.php',
            $this->render("/config/params.php")
        );
        $files[] = new CodeFile(
            $modulePath . '/assets/' . ucfirst(strtolower($this->moduleID)) . 'Asset.php',
            $this->render("/assets/asset.php")
        );
        return $files;
    }

    /**
     * Validates [[moduleClass]] to make sure it is a fully qualified class name.
     */
    public function validateModuleClass()
    {
        if (strpos($this->moduleClass, '\\') === false) {
            $this->addError('moduleClass', 'Module class must be properly namespaced.');
        }
        if (Yii::getAlias($moduleAlias = '@' . str_replace('\\', '/', $this->moduleClass), false) === false) {
            if (!Yii::$app->getModule($moduleId = substr($this->moduleClass, 0, strpos($this->moduleClass, '\\')))) {
                $this->addError('moduleClass', "Module '$moduleId' (parent of '$moduleAlias') doesn't exists.");
            }
        }
        if (empty($this->moduleClass) || substr_compare($this->moduleClass, '\\', -1, 1) === 0) {
            $this->addError('moduleClass', 'Module class name must not be empty. Please enter a fully qualified class name. e.g. "app\\modules\\admin\\Module".');
        }
    }

    public function getModuleAlias()
    {
        return '@' . str_replace('\\', '/', substr($this->moduleClass, 0, strrpos($this->moduleClass, '\\')));
    }

    /**
     * @return boolean the directory that contains the module class
     */
    public function getModulePath()
    {
        return Yii::getAlias($this->getModuleAlias());
    }

    /**
     * @return string the controller namespace of the module.
     */
    public function getControllerNamespace()
    {
        return substr($this->moduleClass, 0, strrpos($this->moduleClass, '\\')) . '\controllers';
    }

    /**
     * @return string the controller namespace of the module.
     */
    public function getAssetsNamespace()
    {
        return substr($this->moduleClass, 0, strrpos($this->moduleClass, '\\')) . '\assets';
    }
}
