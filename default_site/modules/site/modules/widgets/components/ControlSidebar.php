<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 01.04.16 10:45
 */

namespace site\modules\widgets\components;

use yii\base\Widget;

class ControlSidebar extends Widget
{
    public function run()
    {
        return $this->render('@site/modules/widgets/views/control_sidebar.php');
    }
}
