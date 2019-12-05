<?php

use yii\db\Migration;

/**
 * Class m180331_155054_add_unique_key_to_table_day_report_trip_transport
 */
class m180331_155054_add_unique_key_to_table_day_report_trip_transport extends Migration
{
    public function up()
    {
        $this->alterColumn('day_report_trip_transport', 'trip_transport_id', $this->integer()->comment('Траспорт на рейсе')->unique());
    }

    public function down()
    {
        $this->alterColumn('day_report_trip_transport', 'trip_transport_id', $this->integer()->comment('Траспорт на рейсе'));
    }

}
