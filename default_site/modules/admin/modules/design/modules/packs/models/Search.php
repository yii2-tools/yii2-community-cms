<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 04.04.16 8:10
 */

namespace admin\modules\design\modules\packs\models;

use Yii;
use yii\base\Model;
use yii\data\DataProviderInterface;
use yii\data\ActiveDataProvider;
use design\modules\packs\models\DesignPack;

/**
 * Class Search
 * @package admin\modules\design\modules\packs\models
 */
class Search extends Model
{
    /** @var string */
    public $name;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'title' => [['title'], 'default']
        ];
    }

    /**
     * @param array $params
     * @return DataProviderInterface
     */
    public function search($params = [])
    {
        $query = DesignPack::find();

        if ($this->load($params) && $this->validate()) {
            $query->andFilterWhere(['like', 'name', $this->name]);
        }

        return Yii::createObject([
            'class' => ActiveDataProvider::className(),
            'query' => $query,
        ]);
    }
}
