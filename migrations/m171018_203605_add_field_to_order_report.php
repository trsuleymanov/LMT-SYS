<?php

use yii\db\Migration;

class m171018_203605_add_field_to_order_report extends Migration
{
    public function up()
    {
        $this->addColumn('order_report', 'day_report_trip_transport_id', $this->integer()->after('id'));
    }

    public function down()
    {
        $this->dropColumn('order_report', 'day_report_trip_transport_id');
    }
}
