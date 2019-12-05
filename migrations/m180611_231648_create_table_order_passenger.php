<?php

use yii\db\Migration;

/**
 * Class m180611_231648_create_table_order_passenger
 */
class m180611_231648_create_table_order_passenger extends Migration
{
    public function up()
    {
        $this->createTable('order_passenger', [
            'id' => $this->primaryKey(),
            'order_id' => $this->integer()->comment('Заказ'),
            'passenger_id' => $this->integer()->comment('Пассажир'),
        ]);
    }

    public function down()
    {
        $this->dropTable('order_passenger');
    }
}
