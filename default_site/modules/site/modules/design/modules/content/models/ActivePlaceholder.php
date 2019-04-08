<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 23.03.2016 14:20
 * via Gii Model Generator
 */

namespace design\modules\content\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\QueryInterface;
use yii\behaviors\TimestampBehavior;
use app\modules\routing\models\Route;
use design\modules\content\helpers\PlaceholderHelper;
use design\modules\content\interfaces\PlaceholderInterface;
use design\modules\content\traits\PlaceholderTrait;
use design\modules\content\models\query\ActivePlaceholderQuery;

/**
 * This is the model class for table "{{%design_content_placeholders}}".
 *
 * @property string $name
 * @property integer $type
 * @property resource $content
 * @property bool $status
 * @property integer $created_at
 * @property integer $updated_at
 */
abstract class ActivePlaceholder extends ActiveRecord implements PlaceholderInterface
{
    use PlaceholderTrait {
        PlaceholderTrait::activate          as activateBase;
        PlaceholderTrait::configure         as configureBase;
        PlaceholderTrait::evaluateChild     as evaluateChildBase;
        PlaceholderTrait::ensureChilds      as ensureChildsBase;
        PlaceholderTrait::ensureChildsReady as ensureChildsReadyBase;
        PlaceholderTrait::ensureChildReady  as ensureChildReadyBase;
        PlaceholderTrait::getChilds         as getChildsBase;
    }

    const CACHE_DEPENDENCY = <<<SQL
        SELECT COUNT(*), MAX([[updated_at]])
        FROM {{%design_content_placeholders}}
        WHERE [[updated_at]] > 0
SQL;

    const TYPE_EXPRESSION   = 1;
    const TYPE_VIEW         = 2;
    const TYPE_WIDGET       = 3;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%design_content_placeholders}}';
    }

    /**
     * Relation table childs
     * @return string
     */
    public static function tableNameRelationChilds()
    {
        return '{{%design_content_placeholders_childs}}';
    }

    /**
     * Relation table routes
     * @return string
     */
    public static function tableNameRelationRoutes()
    {
        return '{{%design_content_placeholders_routes}}';
    }

    /**
     * Relation table routes ON condition
     * @return array
     */
    public static function relationTableRoutesOn()
    {
        return ['name', Route::FIELD_ROUTE];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className()
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'type', 'content', 'status', 'created_at', 'updated_at'], 'required'],
            [['type', 'status', 'created_at', 'updated_at'], 'integer'],
            [['content'], 'string'],
            [['name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('app', 'Name'),
            'type' => Yii::t('app', 'Type'),
            'content' => Yii::t('app', 'Content'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        return $this->configureBase();
    }

    /**
     * @inheritdoc
     */
    protected function evaluateChild(PlaceholderInterface $child)
    {
        return $this->evaluateChildBase($child);
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @inheritdoc
     */
    public function activate()
    {
        $this->status = PlaceholderHelper::STATUS_ACTIVE;
        if (!$this->save(false)) {
            return false;
        }

        return $this->activateBase();
    }

    /**
     * @inheritdoc
     */
    public function getChilds()
    {
        return array_merge($this->getChildsBase(), $this->contentPlaceholders);
    }

    /**
     * @inheritdoc
     */
    protected function ensureChilds()
    {
        // force load related records
        $this->contentPlaceholders;

        $this->ensureChildsBase();
    }

    /**
     * @inheritdoc
     */
    protected function ensureChildsReady()
    {
        $this->ensureChildsReadyBase();

        foreach ($this->contentPlaceholders as $child) {
            $this->ensureChildReadyBase($child);
        }
    }

    /**
     * @inheritdoc
     */
    public static function instantiate($row)
    {
        return PlaceholderHelper::instantiateByArray($row);
    }

    /**
     * Childs relation condition
     * @return QueryInterface
     * @see http://www.yiiframework.com/doc-2.0/guide-db-active-record.html#junction-table
     */
    public function getContentPlaceholders()
    {
        return $this->hasMany(static::className(), ['name' => 'name'])
            ->viaTable(static::tableNameRelationChilds(), ['parent_name' => 'name']);
    }

    /**
     * @inheritdoc
     */
    public function delete()
    {
        $db = $this->getDb();
        $transaction = $db->beginTransaction();
        try {
            if (($affected = parent::delete()) === false) {
                throw new \UnexpectedValueException('delete operation failed');
            }
            $db->createCommand()
                ->delete(static::tableNameRelationChilds(), ['parent_name' => $this->name])
                ->execute();
            $on = static::relationTableRoutesOn();
            $db->createCommand()
                ->delete(static::tableNameRelationRoutes(), [$on[0] => $this->{$on[0]}])
                ->execute();
            $transaction->commit();

            return $affected;
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error($e, __METHOD__);

            return false;
        }
    }

    /**
     * @inheritdoc
     * @return ActivePlaceholderQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ActivePlaceholderQuery(get_called_class());
    }
}
