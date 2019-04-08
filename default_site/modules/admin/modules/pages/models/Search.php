<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 24.04.16 14:10
 */

namespace admin\modules\pages\models;

use Yii;
use yii\base\Model;
use yii\data\DataProviderInterface;
use yii\data\ActiveDataProvider;
use site\modules\pages\models\Page;

/**
 * Class Search
 * @package admin\modules\pages\models
 */
class Search extends Model
{
    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $route_id;

    /**
     * @var integer
     */
    public $created_at;

    /**
     * @var integer
     */
    public $updated_at;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'title' => [['title', 'route_id'], 'default'],
            'timestampDefault' => [['created_at', 'updated_at'], 'default', 'value' => null],
        ];
    }

    /**
     * @param array $params
     * @return DataProviderInterface
     */
    public function search($params = [])
    {
        $query = Page::find();

        if ($this->load($params) && $this->validate()) {
            $query->andFilterWhere(['like', 'title', $this->title]);

            if (!empty($this->route_id)) {
                $query->joinWith(['route r'], true, 'INNER JOIN')
                    ->andWhere(['like', 'r.url_pattern', $this->route_id]);
            }

            if (!empty($this->created_at)) {
                $date = strtotime($this->created_at);
                $query->andFilterWhere(['between', 'created_at', $date, $date + 3600 * 24]);
            }

            if (!empty($this->updated_at)) {
                $date = strtotime($this->updated_at);
                $query->andFilterWhere(['between', 'updated_at', $date, $date + 3600 * 24]);
            }
        }

        return Yii::createObject([
            'class' => ActiveDataProvider::className(),
            'query' => $query,
        ]);
    }
}
