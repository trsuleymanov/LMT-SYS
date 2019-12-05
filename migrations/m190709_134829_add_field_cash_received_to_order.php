<?php

use yii\db\Migration;

/**
 * Class m190709_134829_add_field_cash_received_to_order
 */
class m190709_134829_add_field_cash_received_to_order extends Migration
{
    public function up()
    {
        $this->addColumn('order', 'cash_received', $this->boolean()->defaultValue(0)->comment('Деньги за заказ получены')->after('penalty_cash_back'));
    }

    public function down()
    {
        $this->dropColumn('order', 'cash_received');
    }
}
