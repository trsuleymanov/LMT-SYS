<?php

use yii\db\Migration;

/**
 * Class m190929_145336_add_new_hand_over_field_to_waybill
 */
class m190929_145336_add_new_hand_over_field_to_waybill extends Migration
{
    public function up()
    {
        $this->addColumn('transport_waybill', 'set_hand_over_b1_operator_id', $this->integer()->comment('Оператор установивший сумму оплату b1')->after('hand_over_b1_data'));
        $this->addColumn('transport_waybill', 'set_hand_over_b1_time', $this->integer()->comment('Время установки суммы оплаты b1')->after('set_hand_over_b1_operator_id'));
        $this->addColumn('transport_waybill', 'set_hand_over_b2_operator_id', $this->integer()->comment('Оператор установивший сумму оплату b2')->after('hand_over_b2_data'));
        $this->addColumn('transport_waybill', 'set_hand_over_b2_time', $this->integer()->comment('Время установки суммы оплаты b2')->after('set_hand_over_b2_operator_id'));
    }

    public function down()
    {
        $this->dropColumn('transport_waybill', 'hand_over_b1_operator_id');
        $this->dropColumn('transport_waybill', 'set_hand_over_b1_time');
        $this->dropColumn('transport_waybill', 'hand_over_b2_operator_id');
        $this->dropColumn('transport_waybill', 'set_hand_over_b2_time');
    }
}
