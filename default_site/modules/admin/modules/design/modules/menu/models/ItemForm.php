<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 10.04.16 14:19
 */

namespace admin\modules\design\modules\menu\models;

use Yii;
use yii\base\Model;
use design\modules\menu\models\MenuItem;
use yii\helpers\ArrayHelper;
use yii\helpers\HtmlPurifier;

/**
 * Class ItemForm
 * @package admin\modules\design\modules\menu\models
 */
class ItemForm extends Model
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    /** @var string */
    public $label;

    /** @var string */
    public $url;

    /** @var boolean */
    public $is_route;

    /** @var integer */
    public $route_id;

    /** @var MenuItem */
    private $item;

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'label' => Yii::t('app', 'Title'),
            'url_to' => Yii::t('app', 'Url'),
            'is_route' => Yii::t('app', 'Route'),
            'route_id' => Yii::t('app', 'Page'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return array_merge(parent::scenarios(), [
            static::SCENARIO_CREATE => ['label', 'url', 'is_route', 'route_id'],
            static::SCENARIO_UPDATE => ['label', 'url', 'is_route', 'route_id'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['is_route'], 'boolean'],
            [['route_id'], 'integer'],
            [['label'], 'required'],
            [['url'], 'required', 'when' => function ($model) {
                return !(int)$model->is_route;
            }],
            [['route_id'], 'required', 'when' => function ($model) {
                return (int)$model->is_route;
            }],
            [['label', 'url'], 'string', 'max' => 255]
        ];
    }

    /**
     * @param MenuItem $item
     */
    public function setItem(MenuItem $item)
    {
        if (!($this->item = $item)) {
            return;
        }

        $this->label = $item->label;

        if ($this->is_route = $item->is_route) {
            $this->route_id = $item->url_to;
        } else {
            $this->url = $item->url_to;
        }
    }

    /**
     * @inheritdoc
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        if (empty($this->item)) {
            return $this->create();
        }

        return $this->update();
    }

    protected function create()
    {
        $this->item = Yii::createObject(MenuItem::className());
        $this->item->setScenario(MenuItem::SCENARIO_CREATE);
        $this->loadModel();

        return $this->item->save();
    }

    protected function update()
    {
        $this->item->setScenario(MenuItem::SCENARIO_UPDATE);
        $this->loadModel();

        return $this->item->save();
    }

    protected function loadModel()
    {
        $this->item->label = HtmlPurifier::process($this->label);
        $this->item->is_route = $this->is_route;
        $this->item->url_to = $this->is_route ? $this->route_id : HtmlPurifier::process($this->url);
    }

    /**
     * Routes for dropdown list field (route_id).
     */
    public function getRoutesList()
    {
        return ArrayHelper::map(Yii::$app->router->getCompleteRoutes(), 'id', function ($route, $defaultValue) {
            return ($description = $route->getDescription()) ? $description : $defaultValue;
        });
    }
}
