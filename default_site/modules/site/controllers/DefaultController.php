<?php

namespace app\modules\site\controllers;

use Yii;
use yii\data\Pagination;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\modules\site\components\Controller;
use site\modules\news\Module;
use site\modules\news\Finder as NewsFinder;
use site\modules\news\assets\NewsAsset;
use admin\modules\users\helpers\RbacHelper;

/**
 * Class DefaultController
 * @package app\modules\site\controllers
 */
class DefaultController extends Controller
{
    /**
     * @var NewsFinder
     */
    public $newsFinder;

    /**
     * @inheritdoc
     */
    public function __construct($id, $module, NewsFinder $newsFinder, $config = [])
    {
        $this->newsFinder = $newsFinder;
        parent::__construct($id, $module, $config);
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array_replace_recursive(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ]);
    }

    public function actions()
    {
        return [
            'index' => [
                'class' => 'yii\tools\components\Action',
                'beforeCallback' => function ($action) {
                    $user = Yii::$app->getUser();

                    if ($user->can(RbacHelper::NEWS_ACCESS)
                        || (($identity = $user->getIdentity()) && $identity->isAdmin)
                    ) {
                        Yii::$container->set(Pagination::className(), [
                            'totalCount' => Module::NEWS_PER_PAGE,
                            'defaultPageSize' => Module::NEWS_PER_PAGE,
                        ]);

                        NewsAsset::register($this->getView());

                        $action->params['news'] = $this->newsFinder->findNews([], true, true, true);
                    }
                }
            ],
            'logout' => [
                'class' => 'yii\tools\components\Action',
                'response' => function ($action) {
                    Yii::$app->user->logout();

                    return $action->controller->goHome();
                }
            ],
            'error' => [
                'class' => 'yii\web\ErrorAction',
                'view' => 'error.twig',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => !YII_ENV_PROD ? 'testme' : null,
            ],
        ];
    }
}
