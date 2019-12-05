<?php

use yii\db\Migration;

/**
 * Class m181103_224019_add_fields_to_transport_waybill
 */
class m181103_224019_add_fields_to_transport_waybill extends Migration
{
    public function up()
    {
        $this->truncateTable('transport_waybill');


        $this->addColumn('transport_waybill', 'trip_comment', $this->string(255)->comment('Комментарий к рейсу')->after('driver_id'));

        // Принят/Не принят/Не представлен - accepted / not_accepted / not_presented
        $this->addColumn('transport_waybill', 'waybill_state', "ENUM('accepted', 'not_accepted', 'not_presented')");

        $this->addColumn('transport_waybill', 'values_fixed_state', "ENUM('accepted', 'not_accepted', 'not_presented')");
        $this->addColumn('transport_waybill', 'gsm', "ENUM('accepted', 'not_accepted', 'not_presented')");

        // Нет/Выдан водителю/Сдан водителем/Внесены данные - no/issued_to_driver / passed_by_driver / data_entered
        $this->addColumn('transport_waybill', 'klpto', "ENUM('none', 'issued_to_driver', 'passed_by_driver', 'data_entered')");
        $this->addColumn('transport_waybill', 'klpto_comment', $this->string(255)->comment('Примечание к КЛПТО'));


        $this->addColumn('transport_waybill', 'trip_event1_id', $this->integer()->comment('Событие 1'));
        $this->addColumn('transport_waybill', 'trip_event1_comment', $this->string(255)->comment('Примечание к событию 1'));

        $this->addColumn('transport_waybill', 'trip_event2_id', $this->integer()->comment('Событие 2'));
        $this->addColumn('transport_waybill', 'trip_event2_comment', $this->string(255)->comment('Примечание к событию 2'));

        $this->addColumn('transport_waybill', 'trip_event3_id', $this->integer()->comment('Событие 3'));
        $this->addColumn('transport_waybill', 'trip_event3_comment', $this->string(255)->comment('Примечание к событию 3'));

        $this->addColumn('transport_waybill', 'trip_event4_id', $this->integer()->comment('Событие 4'));
        $this->addColumn('transport_waybill', 'trip_event4_comment', $this->string(255)->comment('Примечание к событию 4'));

        $this->addColumn('transport_waybill', 'trip_event5_id', $this->integer()->comment('Событие 5'));
        $this->addColumn('transport_waybill', 'trip_event5_comment', $this->string(255)->comment('Примечание к событию 5'));

        $this->addColumn('transport_waybill', 'trip_event6_id', $this->integer()->comment('Событие 6'));
        $this->addColumn('transport_waybill', 'trip_event6_comment', $this->string(255)->comment('Примечание к событию 6'));

        $this->addColumn('transport_waybill', 'trip_event7_id', $this->integer()->comment('Событие 7'));
        $this->addColumn('transport_waybill', 'trip_event7_comment', $this->string(255)->comment('Примечание к событию 7'));

        $this->addColumn('transport_waybill', 'trip_event8_id', $this->integer()->comment('Событие 8'));
        $this->addColumn('transport_waybill', 'trip_event8_comment', $this->string(255)->comment('Примечание к событию 8'));
    }

    public function down()
    {
        $this->truncateTable('transport_waybill');

        $this->dropColumn('transport_waybill', 'trip_comment');
        $this->dropColumn('transport_waybill', 'waybill_state');
        $this->dropColumn('transport_waybill', 'values_fixed_state');
        $this->dropColumn('transport_waybill', 'gsm');
        $this->dropColumn('transport_waybill', 'klpto');
        $this->dropColumn('transport_waybill', 'klpto_comment');

        $this->dropColumn('transport_waybill', 'trip_event1_id');
        $this->dropColumn('transport_waybill', 'trip_event1_comment');

        $this->dropColumn('transport_waybill', 'trip_event2_id');
        $this->dropColumn('transport_waybill', 'trip_event2_comment');

        $this->dropColumn('transport_waybill', 'trip_event3_id');
        $this->dropColumn('transport_waybill', 'trip_event3_comment');

        $this->dropColumn('transport_waybill', 'trip_event4_id');
        $this->dropColumn('transport_waybill', 'trip_event4_comment');

        $this->dropColumn('transport_waybill', 'trip_event5_id');
        $this->dropColumn('transport_waybill', 'trip_event5_comment');

        $this->dropColumn('transport_waybill', 'trip_event6_id');
        $this->dropColumn('transport_waybill', 'trip_event6_comment');

        $this->dropColumn('transport_waybill', 'trip_event7_id');
        $this->dropColumn('transport_waybill', 'trip_event7_comment');

        $this->dropColumn('transport_waybill', 'trip_event8_id');
        $this->dropColumn('transport_waybill', 'trip_event8_comment');
    }
}
