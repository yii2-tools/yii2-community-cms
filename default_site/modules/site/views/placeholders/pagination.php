<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 06.05.16 1:30
 */

use yii\data\Pagination;
use yii\widgets\LinkPager;
use app\helpers\Html;

/** @var Pagination $pagination */
$pagination = Yii::$container->get(Pagination::className());

?>

<?php if (!empty($pagination->totalCount)): ?>
    <?= LinkPager::widget([
        'pagination' => $pagination,
        'firstPageLabel' => Html::faIcon('angle-double-left'),
        'prevPageLabel' => Html::faIcon('angle-left'),
        'nextPageLabel' => Html::faIcon('angle-right'),
        'lastPageLabel' => Html::faIcon('angle-double-right'),
    ]) ?>
<?php endif ?>
