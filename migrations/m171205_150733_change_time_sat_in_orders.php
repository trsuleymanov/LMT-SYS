<?php

use yii\db\Migration;

class m171205_150733_change_time_sat_in_orders extends Migration
{
    public function up()
    {
        $this->alterColumn('order', 'time_sat', $this->integer()->comment('Время посадки в машину (неподтвержденное)'));
        $this->addColumn('order', 'confirmed_time_sat', $this->integer()->comment('Время посадки в машину (подтвержденное)')->after('time_sat'));
        $this->addColumn('order', 'confirmed_time_satter_user_id', $this->integer()->comment('Пользователь нажавший кнопку "Подтвердить посадку"')->after('confirmed_time_sat'));
    }

    public function down()
    {
        $this->alterColumn('order', 'time_sat', $this->integer()->comment('Время посадки в машину'));
        $this->dropColumn('order', 'confirmed_time_sat');
        $this->dropColumn('order', 'confirmed_time_satter_user_id');
    }
}
