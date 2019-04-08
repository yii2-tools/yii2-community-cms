<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 06.05.16 19:55
 */

namespace admin\modules\forum\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\DataProviderInterface;
use yii\db\ActiveQuery;

/**
 * Class Search
 * @package admin\modules\forum\models
 */
abstract class Search extends Model
{
    /** @var string */
    public $modelClass;

    /** @var string */
    public $title;

    /** @var int */
    public $created_at;

    /** @var int */
    public $updated_at;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'title' => [['title', 'created_at', 'updated_at'], 'default'],
        ];
    }

    /**
     * @param array $params
     * @return DataProviderInterface
     */
    public function search($params = [])
    {
        $modelClass = $this->modelClass;
        $query = $modelClass::find();

        if ($this->load($params) && $this->validate()) {
            $this->filter($query);
        }

        return Yii::createObject([
            'class' => ActiveDataProvider::className(),
            'query' => $query,
        ]);
    }

    /**
     * @param ActiveQuery $query
     * @return ActiveQuery $query
     */
    protected function filter($query)
    {
        $query->andFilterWhere(['like', 'title', $this->title]);

        if (!empty($this->created_at)) {
            $date = strtotime($this->created_at);
            $query->andFilterWhere(['between', 'created_at', $date, $date + 3600 * 24]);
        }

        if (!empty($this->updated_at)) {
            $date = strtotime($this->updated_at);
            $query->andFilterWhere(['between', 'updated_at', $date, $date + 3600 * 24]);
        }

        return $query;
    }
}
