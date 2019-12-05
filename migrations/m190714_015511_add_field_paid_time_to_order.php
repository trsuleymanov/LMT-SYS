<?php

use yii\db\Migration;

/**
 * Class m190714_015511_add_field_paid_time_to_order
 */
class m190714_015511_add_field_paid_time_to_order extends Migration
{
    public function up()
    {
        $this->addColumn('order', 'paid_time', $this->integer()->comment('Время оплаты')->after('paid_summ'));
    }

    public function down()
    {
        $this->dropColumn('order', 'paid_time');
    }
}
