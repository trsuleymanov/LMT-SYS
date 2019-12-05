<?php

use yii\db\Migration;

class m170806_131700_create_table_completing_transport_round_reason extends Migration
{
    public function up()
    {
        $this->createTable('transport_round_completing_reason', [
            'id' => $this->primaryKey(),
            'name' => $this->string(50)->comment('Признак/причина завершения круга'),
        ]);

        $this->BatchInsert('transport_round_completing_reason',
            ['name'],
            [
                ['поставлен на рейс, подтвержден'],
                ['выехал из города базирования'],
                ['выехал в сторону города базирования'],
                ['успешно завершил круг'],
            ]
        );


        $this->addColumn('day_report_trip_transport', 'transport_round_is_completed', $this->boolean()->defaultValue(0)->comment('Круг т/с завершен')->after('transport_sender_fio'));
        $this->addColumn('day_report_trip_transport', 'transport_round_completing_reason_id', $this->integer()->comment('Причина завершения круга т/с')->after('transport_round_is_completed'));
    }

    public function down()
    {
        $this->dropColumn('day_report_trip_transport', 'transport_round_is_completed');
        $this->dropColumn('day_report_trip_transport', 'transport_round_completing_reason_id');

        $this->dropTable('transport_round_completing_reason');
    }
}
