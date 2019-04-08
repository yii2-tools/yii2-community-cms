<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 21.03.2016 16:49
 * via Gii Module Generator
 */

namespace site\modules\pages;

use Yii;
use yii\filters\AccessControl;
use app\modules\site\components\Module as BaseModule;
use admin\modules\users\helpers\RbacHelper;

class Module extends BaseModule
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['?', '@'],
                        'matchCallback' => function () {
                            $user = Yii::$app->getUser();

                            return $user->can(RbacHelper::PAGES_ACCESS)
                                || (($identity = $user->getIdentity()) && $identity->isAdmin);
                        },
                    ]
                ],
            ],
        ]);
    }
}
