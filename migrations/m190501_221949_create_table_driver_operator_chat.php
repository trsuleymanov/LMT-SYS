<?php

use yii\db\Migration;

/**
 * Class m190501_221949_create_table_driver_operator_chat
 */
class m190501_221949_create_table_driver_operator_chat extends Migration
{
    public function up()
    {
        $this->createTable('driver_operator_chat', [
            'id' => $this->primaryKey(),
            'trip_transport_id' => $this->integer()->comment('trip_transport_id'),
            'driver_id' => $this->integer()->comment('Водитель'),
            'operator_id' => $this->integer()->comment('Оператор'),
            'message_from_driver' => $this->string(255)->comment('Сообщение водителя'),
            'message_from_driver_at' => $this->integer()->comment('Время прихода на сервер сообщения водителя'),
            'answer_from_operator' => $this->string(255)->comment('Ответ оператора'),
            'answer_from_operator_at' => $this->integer()->comment('Время создания ответа оператора'),
            'driver_is_read_at' => $this->integer()->defaultValue(0)->comment('Время когда водитель "прочитал" ответ диспетчера'),
        ]);
    }

    public function down()
    {
        $this->dropTable('driver_operator_chat');
    }
}
