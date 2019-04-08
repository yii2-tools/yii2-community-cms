<?php
/* @var \yii\web\View $this */
/* @var \app\modules\gii\generators\module\Generator $generator */

echo "<?php\n";
?>

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: <?= date('d.m.Y H:i') . PHP_EOL ?>
 * via Gii <?= $generator->getName() . PHP_EOL ?>
 */

use yii\bootstrap\Html;
use yii\bootstrap\Nav;
use app\helpers\ModuleHelper;
use app\helpers\RouteHelper;

?>

<?= '<?=' ?> Html::beginTag('div', ['class' => 'row']) ?>
    <?= '<?=' ?> Html::beginTag('div', ['class' => 'col-md-12']) ?>
        <?= '<?=' ?> Nav::widget([
            'options' => [
                'class' => 'nav-tabs'
            ],
            'items' => [
                /*[
                    'label'   => Yii::t(ModuleHelper::ADMIN_USERS, 'Users'),
                    'url'     => [RouteHelper::ADMIN_USERS_MANAGEMENT],
                ],
                [
                    'label' => Yii::t(ModuleHelper::ADMIN_USERS, 'Create'),
                    'items' => [
                        [
                            'label'   => Yii::t(ModuleHelper::ADMIN_USERS, 'New user'),
                            'url'     => [RouteHelper::ADMIN_USERS_MANAGEMENT_CREATE],
                            'visible' => true,
                        ],
                        [
                            'label' => Yii::t(ModuleHelper::ADMIN_USERS, 'New role'),
                            'url'   => [RouteHelper::ADMIN_USERS_ROLES_CREATE]
                        ],
                        [
                            'label' => Yii::t(ModuleHelper::ADMIN_USERS, 'New permission'),
                            'url'   => [RouteHelper::ADMIN_USERS_PERMISSIONS_CREATE],
                            'visible' => YII_ENV_DEV,
                        ]
                    ]
                ]*/
            ]
        ]) ?>
    <?= '<?=' ?> Html::endTag('div') ?>
<?= '<?=' ?> Html::endTag('div') ?>