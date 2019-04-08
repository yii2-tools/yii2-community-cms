<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 01.04.16 4:53
 */

/**
 * @var \yii\web\View $this
 */

use yii\bootstrap\Html;
use app\helpers\ModuleHelper;
use app\helpers\RouteHelper;
use site\modules\widgets\components\ControlSidebar;
use site\modules\widgets\components\MenuItems;

$designModule = Yii::$app->getModule(ModuleHelper::DESIGN);

// @todo remove
$this->params['alte_dir'] = Yii::$app->assetManager->getPublishedUrl('@vendor/almasaeed2010/adminlte/dist');

if (!isset($this->params['user']) && ($this->params['user'] = Yii::$app->getUser()->getIdentity())) {
    $this->params['profile'] = $this->params['user']->profile;
}

$js = <<<JAVASCRIPT
    function fixCollapse() {
        $('style.fix-collapse').remove();
        if ($('.main-header .navbar-collapse').height() > 50) {
            $('head').append('<style class="fix-collapse">@media (max-width:99999px){.navbar-header{float:none}.navbar-toggle{display:block}.navbar-collapse.collapse{display:none!important}.navbar-collapse.collapse.in{display:block!important}}</style>');
            return true;
        }
        return false;
    }
    function fixCollapseDynamic(interval, ticks, stopCallback) {
        window.fixCollapseInterval = setInterval(function() {
            if (--ticks < 1 || stopCallback()) {
                clearInterval(window.fixCollapseInterval);
            }
        }, interval);
    }
    $(function() {
        fixCollapse();
        $(window).resize(fixCollapse);
        $(document).on('collapsed.pushMenu', function (event) {
            fixCollapseDynamic(5, $.AdminLTE.options['animationSpeed'] / 5, function() {
                return !fixCollapse();
            });
        });
        $(document).on('expanded.pushMenu', function (event) {
            fixCollapseDynamic(5, $.AdminLTE.options['animationSpeed'] / 5 , function() {
                return fixCollapse();
            });
        });
    });
JAVASCRIPT;

$this->registerJs($js);

?>

<header class="main-header">
    <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
        <?= Html::a('<span class="logo-mini">' . Html::icon('home') . '</span><span class="logo-lg">' . $designModule->params['title'] . '</span>', Yii::$app->homeUrl, ['class' => 'navbar-brand logo']) ?>

        <?php if ($this->params['user'] && $this->params['user']->getIsAdmin()): ?>
        <a href="#" class="sidebar-toggle hidden-xs" data-toggle="offcanvas" role="button">
            <span class="sr-only">Admin</span>
        </a>
        <?php endif ?>

        <div class="navbar-header">
            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">

                    <?php foreach ($content as $html): ?>
                        <?= $html ?>
                    <?php endforeach ?>

                    <?php if ($this->params['user']): ?>
                        <li class="dropdown user user-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <?php if (!empty($this->params['profile']->image_url)) : ?>
                                    <img src="<?= $this->params['profile']->image_url ?>" class="user-image" alt="<?= Html::encode($this->params['profile']->fullName) ?>"/>
                                <?php else : ?>
                                    <canvas width="50" height="50" data-jdenticon-hash="<?= md5($this->params['profile']->fullName) ?>" class="user-image"></canvas>
                                <?php endif ?>
                                <span class="hidden-xs"><?= Html::encode($this->params['profile']->fullName) ?></span>
                            </a>
                            <ul class="dropdown-menu">
                                <!-- User image -->
                                <li class="user-header">
                                    <?php if (!empty($this->params['profile']->image_url)) : ?>
                                        <img src="<?= $this->params['profile']->image_url ?>" class="img-circle" alt="<?= Html::encode($this->params['profile']->fullName) ?>"/>
                                    <?php else : ?>
                                        <canvas width="45" height="45" data-jdenticon-hash="<?= md5($this->params['profile']->fullName) ?>"></canvas>
                                    <?php endif ?>

                                    <p>
                                        <?= Html::encode($this->params['profile']->fullName) ?>
                                    </p>

                                    <?php if (!empty($this->params['user']->roles)): $c=count($this->params['user']->roles); ?>
                                    <p>
                                        <?php foreach ($this->params['user']->roles as $role): ?>
                                            <?= $role->name ?><?php if (--$c > 0): ?>,<?php endif ?>
                                        <?php endforeach ?>
                                    </p>
                                    <?php endif ?>

                                    <p>
                                        <small><?= Yii::t(ModuleHelper::USERS, 'Member since {0, date}', $this->params['user']->created_at) ?></small>
                                    </p>
                                </li>
                                <!-- Menu Footer-->
                                <li class="user-footer">
                                    <div class="pull-left">
                                        <?= Html::a(Yii::t(ModuleHelper::USERS, 'Profile'), [RouteHelper::SITE_USERS_SETTINGS_PROFILE], [
                                                'class' => 'btn btn-default btn-flat'
                                            ]) ?>
                                    </div>
                                    <div class="pull-right">
                                        <?= Html::a(
                                            Yii::t(ModuleHelper::USERS, 'Logout'),
                                            [RouteHelper::SITE_USERS_LOGOUT],
                                            ['data-method' => 'post', 'class' => 'btn btn-default btn-flat']
                                        ) ?>
                                    </div>
                                </li>
                            </ul>
                        </li>

                        <li class="hidden-xs">
                            <!-- <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a> -->
                        </li>
                    <?php else: ?>
                        <li>
                            <?= Html::a(Yii::t(ModuleHelper::USERS, 'Sign in'), [RouteHelper::SITE_USERS_LOGIN]) ?>
                        </li>
                    <?php endif ?>
                </ul>
            </div>

            <a href="#" class="navbar-toggle menu-toggle pull-left" data-toggle="collapse" data-target=".navbar-collapse" role="button">
                <span class="sr-only">Menu</span>
            </a>
        </div>

        <div class="collapse navbar-collapse">
            <?= MenuItems::widget() ?>
        </div>
    </nav>
</header>

<?//php if ($this->params['user']): ?>
    <?//= ControlSidebar::widget() ?>
<?//php endif ?>
