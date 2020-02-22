<?php

use yii\db\Migration;

/**
 * Class m200222_022848_add_field_cash_received_by_user_id_to_order
 */
class m200222_022848_add_field_cash_received_by_user_id_to_order extends Migration
{
    public function up()
    {
        $this->addColumn('order', 'cash_received_by_user_id', $this->integer()->after('cash_received_time')->comment('Пользователь нажавший кнопку "Деньги получены"'));
    }

    public function down()
    {
        $this->dropColumn('order', 'cash_received_by_user_id');
    }
}
