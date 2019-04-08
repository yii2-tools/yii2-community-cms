<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 31.01.16 16:38
 */

use app\helpers\ModuleHelper;

?>

<footer class="footer <?php if (isset($class)):?><?= $class ?><?php endif ?>">
    <div class="container">
        <div class="row">
            <?php if (isset($content)): ?>
            <?= $content ?>
            <?php else: ?>

            <?php
                if (!isset($copyright)) {
                    try {
                        $copyright = Yii::$app->getModule(ModuleHelper::DESIGN)->params['copyright'];
                    } catch (\Exception $e) {
                        Yii::error($e, __FILE__);
                    }
                }
            ?>

            <div class="col-xs-7 col-xs-offset-1 col-sm-7 col-md-8">
                <?php if (isset($copyright)): ?>
                <?= $copyright ?>
                <?php endif ?>
            </div>

            <div class="col-xs-4 col-sm-4 col-md-3">
                <div class="pull-left hidden-xs">
                    <img src="http://domain.ltd/img/gears.png" style="width:20px;margin-bottom:3px" />
                    <a href="<?= Yii::$app->params['engine_link'] ?>">
                        <?= Yii::$app->params['engine_name'] ?>
                    </a>
                    <?= Yii::$app->version ?>
                </div>

                <div class="pull-left hidden-sm hidden-md hidden-lg">
                    <a href="<?= Yii::$app->params['engine_link'] ?>" title="<?= Yii::$app->params['engine_name'] ?>">
                        <img src="http://domain.ltd/img/gears.png" style="width:20px;margin-bottom:3px" />
                    </a>
                    <?= Yii::$app->version ?>
                </div>
            </div>
            <?php endif ?>
        </div>
    </div>
</footer>