<?php

use yii\db\Migration;

class m170705_035841_change_field_prize_trip_count_in_table_client extends Migration
{
    public function up()
    {
        $this->alterColumn('client', 'prize_trip_count', $this->smallInteger(2)->defaultValue(0)->comment('Количество призовых поездок'));
    }

    public function down()
    {
        $this->alterColumn('client', 'prize_trip_count', $this->smallInteger(2)->comment('Количество призовых поездок'));
    }
}
