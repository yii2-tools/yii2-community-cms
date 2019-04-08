<?php

namespace admin\modules\users\models;

use Yii;
use yii\helpers\ArrayHelper;
use app\helpers\ModuleHelper;

/**
 * Class Role
 * @package admin\modules\users\models
 */
class RoleForm extends AuthItemForm
{
    public function scenarios()
    {
        return array_merge(parent::scenarios(), [
            static::SCENARIO_DELETE => ['name'],
        ]);
    }

    /** @inheritdoc */
    public function rules()
    {
        return array_merge(parent::rules(), [
            'roleNotUsed' => ['name', function ($attribute) {
                $usersParams = Yii::$app->getModule(ModuleHelper::USERS)->params;
                if (in_array($this->$attribute, [$usersParams['default_role'], $usersParams['guest_role']])) {
                    $this->addError($attribute, Yii::t(ModuleHelper::ADMIN_USERS, 'Role used as param value'));
                }
            }, 'on' => [static::SCENARIO_DELETE]]
        ]);
    }

    /** @inheritdoc */
    public function getUnassignedItems()
    {
        return ArrayHelper::map(
            $this->manager->getItems(null, $this->item !== null ? [$this->item->name] : []),
            'name',
            function ($item) {
                return empty($item->description) ? $item->name : $item->name . ' (' . $item->description . ')';
            }
        );
    }

    /** @inheritdoc */
    protected function createItem($name)
    {
        return $this->manager->createRole($name);
    }
}
