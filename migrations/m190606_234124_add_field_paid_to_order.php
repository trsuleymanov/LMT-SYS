<?php

use yii\db\Migration;

/**
 * Class m190606_234124_add_field_paid_to_order
 */
class m190606_234124_add_field_paid_to_order extends Migration
{
    public function up()
    {
        $this->addColumn('order', 'paid_summ', $this->decimal(8, 2)->defaultValue(0)->comment('Оплачено')->after('price'));
        $this->addColumn('order', 'is_paid', $this->boolean()->defaultValue(0)->comment('Заказ полностью оплачен - да/нет')->after('penalty_cash_back'));
    }

    public function down()
    {
        $this->dropColumn('order', 'paid_summ');
        $this->dropColumn('order', 'is_paid');
    }
}
