<?php

namespace admin\modules\users\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use admin\modules\users\components\Role;
use admin\modules\users\components\Item;

/**
 * Class RoleController
 * @package admin\modules\users\controllers
 */
class RolesController extends ItemsControllerAbstract
{
    /** @var string */
    protected $modelClass = 'admin\modules\users\models\RoleForm';
    
    protected $type = Item::TYPE_ROLE;

    /** @inheritdoc */
    public function getItem($name)
    {
        $role = \Yii::$app->authManager->getRole($name);

        if ($role instanceof Role) {
            return $role;
        }

        throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
    }
}
