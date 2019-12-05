<?php

use yii\db\Migration;

class m170923_162047_change_field_plan_trip_transport_id_in_order extends Migration
{
    public function up()
    {
        $this->dropColumn('order', 'plan_trip_transport_id');
        $this->addColumn('order', 'confirm_selected_transport', $this->boolean()->defaultValue(0)->after('radio_group_3')->comment('Клиент согласился с посадкой в выбранное т/с'));
    }

    public function down()
    {
        $this->addColumn('order', 'plan_trip_transport_id', $this->integer()->after('radio_group_3')->comment('Пассажиры заказа планируемо будут посажены в транспорто-рейс trip_transport_id'));
        $this->dropColumn('order', 'confirm_selected_transport');
    }
}
