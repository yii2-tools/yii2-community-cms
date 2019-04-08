<?php

namespace admin\modules\users\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use admin\modules\users\components\Permission;
use admin\modules\users\components\Item;

/**
 * Class PermissionController
 * @package admin\modules\users\controllers
 */
class PermissionsController extends ItemsControllerAbstract
{
    /** @var string */
    protected $modelClass = 'admin\modules\users\models\PermissionForm';
    
    /** @var int */
    protected $type = Item::TYPE_PERMISSION;

    public function behaviors()
    {
        return array_replace_recursive(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update', 'create', 'delete'],
                        'matchCallback' => function ($rule, $action) {
                            return YII_ENV_DEV;
                        }
                    ],
                ],
                'denyCallback' => function ($rule, $action) {
                    throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
                }
            ]
        ]);
    }

    /** @inheritdoc */
    public function getItem($name)
    {
        $role = \Yii::$app->authManager->getPermission($name);

        if ($role instanceof Permission) {
            return $role;
        }

        throw new NotFoundHttpException;
    }
}
