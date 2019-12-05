<?php

use yii\db\Migration;

/**
 * Class m190714_010753_add_field_cash_received_time_to_order
 */
class m190714_010753_add_field_cash_received_time_to_order extends Migration
{
    public function up()
    {
        $this->dropColumn('order', 'cash_received');
        $this->addColumn('order', 'cash_received_time', $this->integer()->comment('Деньги за заказ получены')->after('penalty_cash_back'));
    }

    public function down()
    {
        $this->dropColumn('order', 'cash_received_time');
        $this->addColumn('order', 'cash_received', $this->boolean()->defaultValue(0)->comment('Деньги за заказ получены')->after('penalty_cash_back'));
    }
}
