<?php

use yii\db\Migration;

class m170714_151209_change_order_field_time_getting_into_car extends Migration
{
    public function up()
    {
        $this->dropColumn('order', 'time_getting_into_car');
        $this->addColumn('order', 'time_confirm', $this->integer()->comment('ВРПТ (Время подтверждения)')->after('price'));
    }

    public function down()
    {
        $this->dropColumn('order', 'time_confirm');
        $this->addColumn('order', 'time_getting_into_car', $this->integer()->comment('Время готовности первичного подтверждения (планируемое время посадки в машину)')->after('price'));
    }
}
