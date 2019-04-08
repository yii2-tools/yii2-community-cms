<?php

/**
 * Author: Pavel Petrov <itnelo@gmail.com>
 * Date: 09.04.16 11:09
 */

namespace admin\modules\design\modules\menu;

use Yii;
use yii\base\Object;
use design\modules\menu\models\MenuItem;

/**
 * Class Finder
 * @package admin\modules\design\modules\menu
 */
class Finder extends Object
{
    /**
     * @param $id
     * @return array
     */
    public function findModel($id = null)
    {
        if (isset($id)) {
            return MenuItem::findOne($id);
        }

        return MenuItem::find()->orderBy(['position' => SORT_ASC])->all();
    }
}
