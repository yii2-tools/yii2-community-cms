<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 09.04.16 10:58
 */

namespace admin\modules\design\modules\menu\controllers;

use Yii;
use app\modules\admin\components\Controller;
use yii\tools\crud\Action as BaseCrudAction;
use design\modules\menu\models\MenuItem;
use admin\modules\design\modules\menu\Finder;
use admin\modules\design\modules\menu\assets\MenuAsset;

class DefaultController extends Controller
{
    /**
     * @var Finder
     */
    protected $finder;

    /**
     * @inheritdoc
     */
    public function __construct($id, $module, Finder $finder, $config = [])
    {
        $this->finder = $finder;
        parent::__construct($id, $module, $config);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        MenuAsset::register($this->getView());
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return array_merge(parent::actions(), [
            'index' => [
                'class' => 'yii\tools\crud\ReadAction',
                'model' => MenuItem::className(),
                'modelPolicy' => BaseCrudAction::MODEL_POLICY_NONE,
                'multiple' => true,
                'finder' => $this->finder,
            ],
        ]);
    }
}
