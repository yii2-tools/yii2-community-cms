<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 09.04.16 11:03
 */

namespace admin\modules\design\modules\menu\models;

use Yii;
use yii\base\Model;
use yii\data\DataProviderInterface;
use yii\data\ActiveDataProvider;
use design\modules\menu\models\MenuItem;

/**
 * Class Search
 * @package admin\modules\design\models\menu
 */
class Search extends Model
{
    /** @var string */
    public $label;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'label' => [['label'], 'default']
        ];
    }

    /**
     * @param array $params
     * @return DataProviderInterface
     */
    public function search($params = [])
    {
        $query = MenuItem::find();

        if ($this->load($params) && $this->validate()) {
            $query->andFilterWhere(['like', 'label', $this->label]);
        }

        return Yii::createObject([
            'class' => ActiveDataProvider::className(),
            'query' => $query,
        ]);
    }
}
