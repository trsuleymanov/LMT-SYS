<?php

use yii\db\Migration;

/**
 * Class m181115_214639_add_field_trips_summ_to_waybill
 */
class m181115_214639_add_field_trips_summ_to_waybill extends Migration
{
    public function up()
    {
        $this->addColumn('transport_waybill', 'trips_summ',  $this->decimal(8, 2)->defaultValue(0)->comment('Итого по колонке sum (сумма по рейсам)')->after('trip_transport_end'));
    }

    public function down()
    {
        $this->dropColumn('transport_waybill', 'trips_summ');
    }
}
