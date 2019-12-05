<?php

use yii\db\Migration;

class m171110_233024_rename_order_yandex_point_fields extends Migration
{
    public function up()
    {
        $this->renameColumn('order', 'yandex_point_from', 'yandex_point_from_id');
        $this->renameColumn('order', 'yandex_point_to', 'yandex_point_to_id');
    }

    public function down()
    {
        $this->renameColumn('order', 'yandex_point_from_id', 'yandex_point_from');
        $this->renameColumn('order', 'yandex_point_to_id', 'yandex_point_to');
    }
}
