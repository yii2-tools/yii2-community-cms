<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 22.03.16 1:27
 */

namespace integrations\modules\companyName\models;

use Yii;
use yii\base\Model;
use yii\data\ArrayDataProvider;
use app\helpers\ModuleHelper;
use integrations\modules\companyName\helpers\CompanyNameHelper as IntegrationHelper;

/**
 * Class Search
 * @package admin\modules\plugins\models
 */
abstract class BaseSearch extends Model
{
    /** @var string */
    public $title;

    /** @var string */
    public $descr;

    /** @var string */
    public $current_version;

    /** @var string */
    public $release_dt;

    /** @var string Integration GET method (plugins/get, widgets/get) */
    public $method;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'title' => [['title', 'descr', 'current_version', 'release_dt'], 'default']
        ];
    }

    /**
     * @param  array              $params
     * @return ArrayDataProvider
     */
    public function search($params = [])
    {
        $integrationModule = Yii::$app->getModule(ModuleHelper::INTEGRATIONS . '/' . IntegrationHelper::COMPANY_NAME);
        $models = $integrationModule->configurate($this->method)->getIntegrationData(null, true);
        $dataProvider = \Yii::createObject([
                'class' => ArrayDataProvider::className(),
                'pagination' => [
                    'pageSize' => 10,
                ],
                'sort' => [
                    'attributes' => ['title', 'current_version', 'descr', 'release_dt'],
                ],
            ]);

//        if ($this->load($params) && $this->validate()) {
//            foreach ($this->getAttributes() as $name => $value) {
//                if (!empty($value) && method_exists($this, $filterMethod = 'filter' . ucfirst($name))) {
//                    $models = array_filter($models, [$this, $filterMethod]);
//                }
//            }
//        }

        $dataProvider->allModels = $models;

        return $dataProvider;
    }

    public function activeCount()
    {
        $pluginsData = $this->search()->allModels;
        $activeCount = 0;
        foreach ($pluginsData as $data) {
            if (isset($data['status']) && intval($data['status']) > 0) {
                ++$activeCount;
            }
        }
        return $activeCount;
    }

    /**
     * @param $array
     * @return bool
     */
    public function filterTitle($array)
    {
        return isset($array['title']) && strpos(
            mb_strtolower($array['title'], 'UTF-8'),
            mb_strtolower($this->title, 'UTF-8')
        ) !== false;
    }
}
