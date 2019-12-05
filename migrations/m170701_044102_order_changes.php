<?php

use yii\db\Migration;

class m170701_044102_order_changes extends Migration
{
    public function up()
    {
        $this->dropColumn('order', 'tr_id');
        $this->dropColumn('order', 'parent_id');
        $this->dropColumn('order', 'time_confirm');
        $this->dropColumn('order', 'categ_id');

        $this->addColumn('order', 'plan_trip_transport_id', $this->integer()->comment('Пассажиры заказа планируемо будут посажены в транспорто-рейс trip_transport_id')->after('radio_group_3'));
        $this->addColumn('order', 'fact_trip_transport_id', $this->integer()->comment('Пассажиры заказа планируемо фактически посажены в транспорто-рейс trip_transport_id')->after('plan_trip_transport_id'));

        $this->addColumn('order', 'cancellation_reason_id', $this->integer()->comment('Причина отмены заказа')->after('status_id'));
    }

    public function down()
    {
        $this->dropColumn('order', 'cancellation_reason_id');

        $this->dropColumn('order', 'plan_trip_transport_id');
        $this->dropColumn('order', 'fact_trip_transport_id');

        $this->addColumn('order', 'tr_id', $this->integer()->comment('???')->after('date'));
        $this->addColumn('order', 'parent_id', $this->integer()->comment('Группа ')->after('prize_trip_count'));
        $this->addColumn('order', 'time_confirm', $this->integer()->comment('Время подтверждения')->after('additional_phone_3'));
        $this->addColumn('order', 'categ_id', $this->integer()->comment('Категория ')->after('time_confirm'));
    }
}
