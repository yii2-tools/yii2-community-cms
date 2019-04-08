<?php

namespace admin\modules\users\models;

use Yii;
use yii\base\Model;
use app\helpers\ModuleHelper;
use admin\modules\users\components\Item;
use admin\modules\users\validators\RbacValidator;

/**
 * Class AuthItem
 * @package admin\modules\users\models
 */
abstract class AuthItemForm extends Model
{
    const SCENARIO_DELETE = 'delete';

    /** @var string */
    public $name;

    /** @var string */
    public $description;

    /** @var string */
    public $rule;

    /** @var string[] */
    public $children = [];

    /** @var \admin\modules\users\components\DbManager */
    protected $manager;

    /** @var \admin\modules\users\components\Role|\admin\modules\users\components\Permission */
    protected $item;

    private $oldAttributes = [];

    /** @inheritdoc */
    public function init()
    {
        parent::init();
        $this->manager = \Yii::$app->authManager;
    }

    public function setItem(Item $item)
    {
        $this->item = $item;
        $this->name = $this->item->name;
        $this->description = $this->item->description;
        $this->children = array_keys($this->manager->getChildren($this->item->name));
        if ($this->item->ruleName !== null) {
            $this->rule = get_class($this->manager->getRule($this->item->ruleName));
        }
    }

    /** @inheritdoc */
    public function attributeLabels()
    {
        return [
            'name' => \Yii::t(ModuleHelper::ADMIN_USERS, 'Name'),
            'description' => \Yii::t(ModuleHelper::ADMIN_USERS, 'Description'),
            'children' => \Yii::t(ModuleHelper::ADMIN_USERS, 'Children'),
            'rule' => \Yii::t(ModuleHelper::ADMIN_USERS, 'Rule'),
        ];
    }

    /** @inheritdoc */
    public function scenarios()
    {
        $scenarios = [
            'create' => ['name', 'description', 'children'],
            'update' => ['name', 'description', 'children'],
            static::SCENARIO_DELETE => [],
        ];

        if (YII_ENV_DEV) {
            $scenarios['create'][] = 'rule';
            $scenarios['update'][] = 'rule';
        }

        return array_merge(parent::scenarios(), $scenarios);
    }

    /** @inheritdoc */
    public function rules()
    {
        return [
            ['name', 'required'],
            [['name', 'description', 'rule'], 'trim'],
            ['name', function () {
                if ($this->manager->getItem($this->name) !== null) {
                    $this->addError('name', \Yii::t(ModuleHelper::ADMIN_USERS, 'Auth item with such name already exists'));
                }
            }, 'when' => function () {
                return $this->scenario == 'create'
                    || (mb_strtolower($this->name, 'UTF-8') !== mb_strtolower($this->item->name, 'UTF-8'));
            }],
            ['children', RbacValidator::className()],
            ['rule', function () {
                if (!YII_ENV_DEV) {
                    $this->addError('engine', Yii::t('errors', 'Internal error'));
                    return;
                }
                try {
                    $class = new \ReflectionClass($this->rule);
                } catch (\Exception $ex) {
                    $this->addError('rule', \Yii::t(ModuleHelper::ADMIN_USERS, 'Class "{0}" does not exist', $this->rule));
                    return;
                }
                if ($class->isInstantiable() == false) {
                    $this->addError('rule', \Yii::t(ModuleHelper::ADMIN_USERS, 'Rule class can not be instantiated'));
                }
                if ($class->isSubclassOf('\yii\rbac\Rule') == false) {
                    $this->addError('rule', \Yii::t(ModuleHelper::ADMIN_USERS, 'Rule class must extend "yii\rbac\Rule"'));
                }
            }],
        ];
    }

    public function load($data, $formName = null)
    {
        $attributes = $this->getAttributes();

        if (parent::load($data, $formName)) {
            $this->oldAttributes = $attributes;
            return true;
        }

        return false;
    }

    public function getOldAttribute($name)
    {
        return isset($this->oldAttributes[$name]) ? $this->oldAttributes[$name] : null;
    }

    /**
     * Saves item.
     *
     * @return bool
     */
    public function save()
    {
        if ($this->validate() == false) {
            return false;
        }

        if ($isNewItem = ($this->item === null)) {
            $this->item = $this->createItem($this->name);
        } else {
            $oldName = $this->item->name;
        }

        $this->item->name        = $this->name;
        $this->item->description = $this->description;

        if (!empty($this->rule)) {
            $rule = \Yii::createObject($this->rule);
            if (null === $this->manager->getRule($rule->name)) {
                $this->manager->add($rule);
            }
            $this->item->ruleName = $rule->name;
        } else {
            $this->item->ruleName = null;
        }

        if ($isNewItem) {
            $this->manager->add($this->item);
        } else {
            $this->manager->update($oldName, $this->item);
        }

        $this->updateChildren();

        return true;
    }

    public function delete()
    {
        if ($this->validate() == false) {
            return false;
        }

        return Yii::$app->authManager->remove($this->item);
    }

    /**
     * Updated items children.
     */
    protected function updateChildren()
    {
        $children = $this->manager->getChildren($this->item->name);
        $childrenNames = array_keys($children);

        if (is_array($this->children)) {
            // remove children that
            foreach (array_diff($childrenNames, $this->children) as $item) {
                $this->manager->removeChild($this->item, $children[$item]);
            }
            // add new children
            foreach (array_diff($this->children, $childrenNames) as $item) {
                $this->manager->addChild($this->item, $this->manager->getItem($item));
            }
        } else {
            $this->manager->removeChildren($this->item);
        }
    }

    /**
     * @return array An array of unassigned items.
     */
    abstract public function getUnassignedItems();

    /**
     * @param  string         $name
     * @return \admin\modules\users\components\Item
     */
    abstract protected function createItem($name);
}
