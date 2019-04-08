<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 01.02.16 8:15
 */

namespace app\modules\admin;

use Yii;
use yii\base\BootstrapInterface;
use yii\filters\AccessControl;
use site\modules\design\helpers\ModuleHelper;
use app\helpers\RouteHelper;
use app\components\Module as BaseModule;

class Module extends BaseModule implements BootstrapInterface
{
    /** @var string */
    public $contentLayout = '@admin/views/layouts/module.php';

    /** @inheritdoc */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function () {
                            return Yii::$app->user->identity->getIsAdmin();
                        },
                    ]
                ],
            ],
        ]);
    }

    public function bootstrap($app)
    {
        // Loading required modules from site context.
        Yii::$app->getModule(ModuleHelper::I18N);

        parent::bootstrap($app);

        Yii::$app->getModule(ModuleHelper::DESIGN_MENU);

        Yii::$app->viewPath = $this->viewPath;
        Yii::$app->getErrorHandler()->errorAction = RouteHelper::ADMIN_ERROR;
    }
}
