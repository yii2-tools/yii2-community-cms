<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 05.02.16 21:56
 */

namespace app\modules\booting\components;

use Yii;
use yii\base\Component;
use yii\data\Pagination;
use yii\jui\DatePicker;
use kartik\grid\GridView;
use kartik\grid\DataColumn;
use kartik\grid\ActionColumn;

/**
 * Class Customizer
 * Provides params customization of Yii (and other) env classes (based on di Yii::$container)
 *
 * @package app\modules\booting\components
 */
class Customizer extends Component
{
    /**
     * @return void
     */
    public function customize()
    {
        $this->customizeComponents();
        $this->customizeUi();
    }

    /**
     * Performs customization of application components via DI container.
     */
    public function customizeComponents()
    {
        Yii::$container->setSingleton('yii\tools\interfaces\RequestInterface', 'yii\tools\components\Curl');
    }

    /**
     * Performs customization of UI elements via DI container.
     */
    public function customizeUi()
    {
        Yii::$container->set(GridView::className(), [
            'layout' => "{items}\n{summary}\n{pager}",
            'bordered' => false,
            'striped' => false,
            'persistResize' => YII_ENV_TEST || YII_ENV_PROD,
            'pjaxSettings' => [
                'options' => [
                    'enablePushState' => true,
                ],
                'loadingCssClass' => 'grid-loading',
            ],
            'export' => false,
        ]);

        Yii::$container->set(DataColumn::className(), [
            'vAlign' => GridView::ALIGN_MIDDLE,
        ]);

        Yii::$container->set(ActionColumn::className(), [
            'header' => '',
            'mergeHeader' => false,
            'noWrap' => true,
            'options' => [
                'class' => 'col-md-1'
            ],
            'contentOptions'   => [
                'class' => 'action-column'
            ],
        ]);

        Yii::$container->set(DatePicker::className(), [
            'options' => [
                'class' => 'form-control',
            ],
        ]);

        Yii::$container->set(Pagination::className(), [
            'defaultPageSize' => 10,
        ]);
    }
}
