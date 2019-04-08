<?php

namespace admin\modules\users\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\base\InvalidConfigException;
use yii\base\Model;
use app\helpers\ModuleHelper;
use admin\modules\users\components\DbManager;
use admin\modules\users\validators\RbacValidator;

/**
 * Class Assignment
 * @package admin\modules\users\models
 */
class Assignment extends Model
{
    /** @var array */
    public $items = [];

    /** @var integer */
    public $user_id;

    /** @var boolean */
    public $updated = false;

    /** @var DbManager */
    protected $manager;

    /** @inheritdoc */
    public function init()
    {
        parent::init();
        $this->manager = Yii::$app->authManager;
        if ($this->user_id === null) {
            throw new InvalidConfigException('user_id must be set');
        }

        $this->items = array_keys($this->manager->getItemsByUser($this->user_id));
    }

    /** @inheritdoc */
    public function attributeLabels()
    {
        return [
            'items' => \Yii::t(ModuleHelper::ADMIN_USERS, 'Items'),
        ];
    }

    /** @inheritdoc */
    public function rules()
    {
        return [
            ['user_id', 'required'],
            ['items', RbacValidator::className()],
            ['user_id', 'integer']
        ];
    }

    /**
     * Updates auth assignments for user.
     * @return boolean
     */
    public function updateAssignments()
    {
        if (!$this->validate()) {
            return false;
        }

        if (!is_array($this->items)) {
            $this->items = [];
        }

        $assignedItems = $this->manager->getItemsByUser($this->user_id);
        $assignedItemsNames = array_keys($assignedItems);

        foreach (array_diff($assignedItemsNames, $this->items) as $item) {
            $this->manager->revoke($assignedItems[$item], $this->user_id);
        }

        foreach (array_diff($this->items, $assignedItemsNames) as $item) {
            $this->manager->assign($this->manager->getItem($item), $this->user_id);
        }

        $this->updated = true;

        return true;
    }

    /**
     * Returns all available auth items to be attached to user.
     * @return array
     */
    public function getAvailableItems()
    {
        return ArrayHelper::map($this->manager->getItems(), 'name', function ($item) {
            return empty($item->description)
                ? $item->name
                : $item->name . ' (' . $item->description . ')';
        });
    }
}
