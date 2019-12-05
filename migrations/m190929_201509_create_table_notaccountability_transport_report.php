<?php

use yii\db\Migration;

/**
 * Class m190929_201509_create_table_notaccountability_transport_report
 */
class m190929_201509_create_table_notaccountability_transport_report extends Migration
{
    public function up()
    {
        $this->createTable('notaccountability_transport_report', [
            'id' => $this->primaryKey(),
            'date_of_issue' => $this->integer()->comment('Дата выдачи'),
            'transport_id' => $this->integer()->comment('Траспорт'),
            'driver_id' => $this->integer()->comment('Водитель'),
            'trip_transport_start' => $this->integer()->comment('Рейс стартовый'),
            'trip_transport_end' => $this->integer()->comment('Рейс обратный'),
            'hand_over_b1' => $this->decimal(8, 2)->defaultValue(0)->comment('сдано B1'),
            'hand_over_b1_data' => $this->integer()->comment('Дата (когда сдано B1)'),
            'set_hand_over_b1_operator_id' => $this->integer()->comment('Оператор установивший сумму оплату b1'),
            'set_hand_over_b1_time' => $this->integer()->comment('Время установки суммы оплаты b1'),
            'hand_over_b2' => $this->decimal(8, 2)->defaultValue(0)->comment('сдано B2'),
            'hand_over_b2_data' => $this->integer()->comment('Дата (когда сдано B2)'),
            'set_hand_over_b2_operator_id' => $this->integer()->comment('Оператор установивший сумму оплату b2'),
            'set_hand_over_b2_time' => $this->integer()->comment('Время установки суммы оплаты b2'),
        ]);
    }

    public function down()
    {
        $this->dropTable('notaccountability_transport_report');
    }
}
