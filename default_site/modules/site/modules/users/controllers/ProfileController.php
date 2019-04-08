<?php

namespace site\modules\users\controllers;

use Yii;
use yii\filters\AccessControl;
use app\modules\site\components\Controller;
use site\modules\design\interfaces\ContextInterface;
use site\modules\users\models\User;
use site\modules\users\Finder;

class ProfileController extends Controller
{
    /** @var Finder */
    protected $finder;

    /**
     * @inheritdoc
     */
    public function __construct($id, $module, ContextInterface $designContext, Finder $finder, $config = [])
    {
        $this->finder = $finder;
        parent::__construct($id, $module, $designContext, $config);
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array_replace_recursive(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    ['allow' => true, 'actions' => ['index'], 'roles' => ['@']],
                    ['allow' => true, 'actions' => ['show'], 'roles' => ['?', '@']],
                ],
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'show' => [
                'class' => 'yii\tools\crud\ReadAction',
                'model' => User::className(),
                'searchKey' => 'username',
                'finder' => $this->finder
            ]
        ];
    }
}
