<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 08.05.2016 00:12
 * via Gii Module Generator
 */

namespace site\modules\news;

use Yii;
use yii\filters\AccessControl;
use app\modules\site\components\Module as BaseModule;
use admin\modules\users\helpers\RbacHelper;

class Module extends BaseModule
{
    const NEWS_PER_PAGE = 10;

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

                            return $user->can(RbacHelper::NEWS_ACCESS)
                                || (($identity = $user->getIdentity()) && $identity->isAdmin);
                        },
                    ]
                ],
            ],
        ]);
    }
}
