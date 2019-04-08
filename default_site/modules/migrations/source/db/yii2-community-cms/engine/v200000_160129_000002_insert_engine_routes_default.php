<?php

use yii\db\Schema;
use yii\db\Migration;
use app\modules\routing\models\Route;

class v200000_160129_000002_insert_engine_routes_default extends Migration
{
    public function up()
    {
        $this->batchInsert(
            Route::tableName(),
            require(__DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'engine_routing_columns.php'),
            require(__DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'engine_routing.php')
        );
    }

    public function down()
    {
        $this->delete(
            Route::tableName(),
            '[[id]] IN (1000, 2000, 2100, 2101, 2102, 2200, 2201,
                        2202, 2203, 2300, 2301, 2302, 2303, 2400,
                        2401, 3000, 4000, 4100, 4101, 4200, 4300,
                        4301, 4302, 5000, 5001, 5002, 5003)'
        );
    }
}
