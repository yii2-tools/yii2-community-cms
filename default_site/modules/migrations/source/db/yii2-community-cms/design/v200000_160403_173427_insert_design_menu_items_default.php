<?php

use yii\db\Migration;
use site\modules\design\helpers\ModuleHelper;
use design\modules\menu\models\MenuItem;

class v200000_160403_173427_insert_design_menu_items_default extends Migration
{
    public function up()
    {
        $this->batchInsert(
            MenuItem::tableName(),
            [
                'id', 'label', 'url_to', 'is_route', 'position', 'created_at', 'updated_at'
            ],
            [
                // @todo make route to page with param as identifier
                [1, Yii::t(ModuleHelper::DESIGN, 'About us'), '/about', 0, 0, time(), time()]
            ]
        );
    }

    public function down()
    {
        $ids = "
            '1'
        ";

        $this->delete(MenuItem::tableName(), "[[id]] IN ($ids)");
    }
}
