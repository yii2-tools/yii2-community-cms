<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 01.04.16 6:47
 */

namespace site\modules\widgets\components;

use yii\base\Widget;

class AdminSidebar extends Widget
{
    public function run()
    {
        return $this->render('@site/modules/widgets/views/admin_sidebar.php');
    }
}
