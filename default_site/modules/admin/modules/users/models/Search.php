<?php

namespace admin\modules\users\models;

use Yii;
use yii\base\Model;
use yii\data\ArrayDataProvider;
use yii\db\Query;
use app\helpers\ModuleHelper;
use admin\modules\users\components\Item;

/**
 * Class Search
 * @package admin\modules\users\models
 */
class Search extends Model
{
    /** @var string */
    public $name;
    
    /** @var string */
    public $description;
    
    /** @var string */
    public $rule_name;
    
    /** @var \admin\modules\users\components\DbManager */
    protected $manager;
    
    /** @var int */
    protected $type;

    /** @inheritdoc */
    public function __construct($type = Item::TYPE_ROLE, $config = [])
    {
        parent::__construct($config);
        $this->manager = Yii::$app->authManager;
        $this->type    = $type;
    }

    /**
     * @param int $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }
    
    /** @inheritdoc */
    public function scenarios()
    {
        return [
            'default' => ['name', 'description', 'rule_name'],
        ];
    }

    /** @inheritdoc */
    public function rules()
    {
        return [
            'fieldsSafe' => [['name', 'description', 'rule_name'], 'safe'],
        ];
    }

    /** @inheritdoc */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t(ModuleHelper::USERS, 'Name'),
        ];
    }
    
    /**
     * @param  array              $params
     * @return ArrayDataProvider
     */
    public function search($params = [])
    {
        $dataProvider = \Yii::createObject([
            'class' => ArrayDataProvider::className(),
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort' => [
                'attributes' => ['name', 'description', 'rule_name'],
            ],
        ]);

        $query = (new Query)->select(['name', 'description', 'rule_name'])
            ->andWhere(['type' => $this->type])
            ->from($this->manager->itemTable);

        if ($this->load($params) && $this->validate()) {
            $query->andFilterWhere(['like', 'name', $this->name])
                ->andFilterWhere(['like', 'description', $this->description])
                ->andFilterWhere(['like', 'rule_name', $this->rule_name]);
        }

        $query->andWhere(['=', 'active', 1]);

        $dataProvider->allModels = $query->all($this->manager->db);

        return $dataProvider;
    }
}
