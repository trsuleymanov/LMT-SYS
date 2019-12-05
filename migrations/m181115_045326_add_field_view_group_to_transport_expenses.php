<?php

use yii\db\Migration;

/**
 * Class m181115_045326_add_field_view_group_to_transport_expenses
 */
class m181115_045326_add_field_view_group_to_transport_expenses extends Migration
{
    public function up()
    {
        $this->truncateTable('transport_waybill');
        $this->truncateTable('transport_expenses');
        $this->truncateTable('transport_expenses_detailing');
        $this->addColumn('transport_expenses', 'view_group', "ENUM('typical_expenses', 'other_expenses', 'incoming_payment_requests')");
    }

    public function down()
    {
        $this->dropColumn('transport_expenses', 'view_group');
    }
}
