<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 01.04.16 6:45
 */

use yii\bootstrap\Html;
use app\helpers\ModuleHelper;
use app\modules\admin\components\Module as AdminModule;
use site\modules\widgets\components\AdminSidebarMenu as Menu;

if (!isset($this->params['user']) && ($this->params['user'] = Yii::$app->getUser()->getIdentity())) {
    $this->params['profile'] = $this->params['user']->profile;
}

if ($this->params['user'] && $this->params['user']->getIsAdmin()) {
    $this->params['sidebar_collapse'] = intval($this->params['user']->params['sidebar_collapse']) ? ' sidebar-collapse' : '';

    if (!isset($this->params['sub_modules'])) {
        $this->params['core_module'] = Yii::$app->getModule(ModuleHelper::ADMIN);
        $this->params['sub_modules'] = $this->params['core_module']->getModules(false, true);
    }

    foreach ($this->params['sub_modules'] as $_module) {
        if ($_module instanceof AdminModule) {
            if ($_module_sidebar = $_module->sidebar()) {
                $this->params['sidebar'][] = $_module_sidebar;
            }
        }
    }
}

?>

<?php if ($this->params['user'] && $this->params['user']->getIsAdmin()): ?>
<aside class="main-sidebar">

    <section class="sidebar">

        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <?php if (!empty($this->params['profile']->image_url)) : ?>
                    <img src="<?= $this->params['profile']->image_url ?>" class="img img-circle" alt="<?= Html::encode($this->params['profile']->fullName) ?>"/>
                <?php else : ?>
                    <canvas width="45" height="45" data-jdenticon-hash="<?= md5($this->params['profile']->fullName) ?>" class="img img-circle"
                        <?php if ($this->params['sidebar_collapse']): ?> style="display:none"<?php endif ?>>
                    </canvas>
                <?php endif ?>
            </div>
            <div class="pull-left info">
                <p><?= Html::encode($this->params['profile']->fullName) ?></p>

                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>

        <!-- search form -->
        <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="<?= Yii::t('app', 'Search') ?>" disabled />
              <span class="input-group-btn">
                <button type='submit' name='search' id='search-btn' class="btn btn-flat" disabled>
                    <i class="fa fa-search"></i>
                </button>
              </span>
            </div>
        </form>
        <!-- /.search form -->

        <?= Menu::widget([
            'options' => ['class' => 'sidebar-menu'],
            'items' => array_merge([
                    [
                        'label' => $this->params['core_module']->params['name'],
                        'options' => ['class' => 'header']
                    ],
                ],
                $this->params['sidebar']
            ),
        ]) ?>

    </section>

</aside>
<?php endif ?>
