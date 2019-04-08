<?php

namespace admin\modules\users\components;

use Yii;
use yii\db\Query;
use yii\db\Expression;
use yii\rbac\DbManager as BaseDbManager;
use app\helpers\ModuleHelper;

/**
 * Class DbManager
 * @package admin\modules\users\components
 */
class DbManager extends BaseDbManager implements ManagerInterface
{
    /**
     * @var string the name of the table storing authorization items. Defaults to "auth_item".
     */
    public $itemTable = '{{%users_rbac_items}}';
    /**
     * @var string the name of the table storing authorization item hierarchy. Defaults to "auth_item_child".
     */
    public $itemChildTable = '{{%users_rbac_items_childs}}';
    /**
     * @var string the name of the table storing authorization item assignments. Defaults to "auth_assignment".
     */
    public $assignmentTable = '{{%users_rbac_assignments}}';
    /**
     * @var string the name of the table storing rules. Defaults to "auth_rule".
     */
    public $ruleTable = '{{%users_rbac_rules}}';

    /**
     * @param  int|null $type         If null will return all auth items.
     * @param  array    $excludeItems Items that should be excluded from result array.
     * @return array
     */
    public function getItems($type = null, $excludeItems = [])
    {
        $query = (new Query())
            ->from($this->itemTable);

        if ($type !== null) {
            $query->where(['type' => $type]);
        } else {
            $query->orderBy('type');
        }

        foreach ($excludeItems as $name) {
            $query->andWhere('name != :item', ['item' => $name]);
        }

        $query->andWhere(['=', 'active', 1]);

        $items = [];

        foreach ($query->all($this->db) as $row) {
            $items[$row['name']] = $this->populateItem($row);
        }

        return $items;
    }

    /**
     * Returns both roles and permissions assigned to user.
     *
     * @param  integer $userId
     * @return array
     */
    public function getItemsByUser($userId)
    {
        if (empty($userId)) {
            return [];
        }

        $query = (new Query)->select('b.*')
            ->from(['a' => $this->assignmentTable, 'b' => $this->itemTable])
            ->where('{{a}}.[[item_name]]={{b}}.[[name]]')
            ->andWhere(['a.user_id' => (string) $userId]);

        $roles = [];
        foreach ($query->all($this->db) as $row) {
            $roles[$row['name']] = $this->populateItem($row);
        }
        return $roles;
    }

    /** @inheritdoc */
    public function getItem($name)
    {
        return parent::getItem($name);
    }

    /**
     * @inheritdoc
     */
    public function getChildren($name)
    {
        $query = (new Query)
            ->select(['name', 'type', 'description', 'rule_name', 'data', 'active', 'created_at', 'updated_at'])
            ->from([$this->itemTable, $this->itemChildTable])
            ->where(['parent' => $name, 'name' => new Expression('[[child]]')]);

        $children = [];
        foreach ($query->all($this->db) as $row) {
            $children[$row['name']] = $this->populateItem($row);
        }

        return $children;
    }

    /**
     * @inheritdoc
     */
    protected function populateItem($row)
    {
        $class = $row['type'] == Item::TYPE_PERMISSION ? Permission::className() : Role::className();

        if (!isset($row['data']) || ($data = @unserialize($row['data'])) === false) {
            $data = null;
        }

        return new $class([
            'name' => $row['name'],
            'type' => $row['type'],
            'description' => $row['description'],
            'ruleName' => $row['rule_name'],
            'data' => $data,
            'active' => $row['active'],
            'createdAt' => $row['created_at'],
            'updatedAt' => $row['updated_at'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function createRole($name)
    {
        $role = new Role;
        $role->name = $name;

        return $role;
    }

    /**
     * @inheritdoc
     */
    public function createPermission($name)
    {
        $permission = new Permission();
        $permission->name = $name;

        return $permission;
    }

    /**
     * @inheritdoc
     */
    protected function addItem($item)
    {
        $time = time();
        if ($item->createdAt === null) {
            $item->createdAt = $time;
        }
        if ($item->updatedAt === null) {
            $item->updatedAt = $time;
        }
        $attributes = [
            'name' => $item->name,
            'type' => $item->type,
            'description' => $item->description,
            'rule_name' => $item->ruleName,
            'data' => $item->data === null ? null : serialize($item->data),
            'created_at' => $item->createdAt,
            'updated_at' => $item->updatedAt,
        ];
        if (isset($item->active)) {
            $attributes['active'] = $item->active;
        }

        $transaction = $this->db->beginTransaction();
        try {
            $this->db->createCommand()->insert($this->itemTable, $attributes)->execute();

            $this->invalidateCache();

            Yii::$app->getModule(ModuleHelper::USERS)
                ->params[intval($item->type) === Item::TYPE_ROLE ? 'roles_count' : 'permissions_count'] += 1;

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();

            throw $e;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function updateItem($name, $item)
    {
        if ($item->name !== $name && !$this->supportsCascadeUpdate()) {
            $this->db->createCommand()
                ->update($this->itemChildTable, ['parent' => $item->name], ['parent' => $name])
                ->execute();
            $this->db->createCommand()
                ->update($this->itemChildTable, ['child' => $item->name], ['child' => $name])
                ->execute();
            $this->db->createCommand()
                ->update($this->assignmentTable, ['item_name' => $item->name], ['item_name' => $name])
                ->execute();
        }

        $item->updatedAt = time();

        $this->db->createCommand()
            ->update($this->itemTable, [
                    'name' => $item->name,
                    'description' => $item->description,
                    'rule_name' => $item->ruleName,
                    'data' => $item->data === null ? null : serialize($item->data),
                    'active' => $item->active,
                    'updated_at' => $item->updatedAt,
                ], [
                    'name' => $name,
                ])->execute();

        $this->invalidateCache();

        return true;
    }

    protected function removeItem($item)
    {
        $transaction = $this->db->beginTransaction();
        try {
            parent::removeItem($item);

            Yii::$app->getModule(ModuleHelper::USERS)
                ->params[intval($item->type) === Item::TYPE_ROLE ? 'roles_count' : 'permissions_count'] -= 1;

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();

            throw $e;
        }

        return true;
    }

    public function removeAll()
    {
        $transaction = $this->db->beginTransaction();
        try {
            parent::removeAll();

            $module = Yii::$app->getModule(ModuleHelper::USERS);
            $module->params['roles_count'] = 0;
            $module->params['permissions_count'] = 0;

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();

            throw $e;
        }
    }

    protected function removeAllItems($type)
    {
        $transaction = $this->db->beginTransaction();
        try {
            parent::removeAllItems($type);

            Yii::$app->getModule(ModuleHelper::USERS)
                ->params[intval($type) === Item::TYPE_ROLE ? 'roles_count' : 'permissions_count'] = 0;

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();

            throw $e;
        }
    }
}
