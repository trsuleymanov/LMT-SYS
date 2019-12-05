<?php

use yii\db\Migration;

/**
 * Class m190618_201500_add_paid_summ_fields_to_day_report_tables
 */
class m190618_201500_add_paid_summ_fields_to_day_report_tables extends Migration
{
    public function up()
    {
        $this->addColumn('day_report_trip_transport', 'paid_summ', $this->decimal(8, 2)->defaultValue(0)->comment('Оплачено')->after('proceeds'));
        $this->addColumn('day_report_transport_circle', 'total_paid_summ', $this->decimal(8, 2)->defaultValue(0)->comment('Оплачено')->after('total_proceeds'));
    }

    public function down()
    {
        $this->dropColumn('day_report_trip_transport', 'paid_summ');
        $this->dropColumn('day_report_transport_circle', 'total_paid_summ');
    }

}
