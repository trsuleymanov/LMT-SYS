<?php

use yii\db\Migration;

class m170929_222136_add_field_sort_to_trip_transport extends Migration
{
    public function up()
    {
        $this->addColumn('trip_transport', 'sort', $this->smallInteger()->defaultValue(0)->comment('Сортировка'));
    }

    public function down()
    {
        $this->dropColumn('trip_transport', 'sort');
    }
}
