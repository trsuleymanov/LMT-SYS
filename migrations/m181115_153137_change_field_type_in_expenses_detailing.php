<?php

use yii\db\Migration;

/**
 * Class m181115_153137_change_field_type_in_expenses_detailing
 */
class m181115_153137_change_field_type_in_expenses_detailing extends Migration
{
    public function up()
    {
        $this->truncateTable('transport_expenses_detailing');
        //$this->addColumn('transport_expenses', 'view_group', "ENUM('typical_expenses', 'other_expenses', 'incoming_payment_requests')");
        $this->dropColumn('transport_expenses_detailing', 'type');
        $this->addColumn('transport_expenses_detailing', 'type', "ENUM('work_services', 'details_goods')");
    }

    public function down()
    {
        $this->truncateTable('transport_expenses_detailing');
        $this->dropColumn('transport_expenses_detailing', 'type');
        $this->addColumn('transport_expenses_detailing', 'type', "ENUM('work', 'spare_part', 'detail')");
    }
}
