<?php

use yii\db\Migration;

/**
 * Class m180922_173303_create_table_call
 */
class m180922_173303_create_table_call extends Migration
{
    public function up()
    {
        $this->createTable('call', [
            'id' => $this->primaryKey(),
            'created_at' => $this->integer()->comment('Создан'),
            'callid' => $this->string(36)->comment('код соединения в АТС'),
            'is_opening' => $this->boolean()->defaultValue(0)->comment('Открыт'),
            'user_phone' => $this->string(20)->comment('Телефон оператора'),
            'user_id' => $this->integer()->comment('Оператор'),
            'mobile_ats_login' => $this->string(20)->comment('Логин оператора'),
            //'client_id' => $this->integer()->comment('Клиент'),
            //'client_phone' => $this->integer()->comment('Телефон клиента'),
            'subscriber_phone' => $this->string(20)->comment('Телефон абонента'),
            'subscriber_type' => "ENUM('client', 'driver')",
            'subscriber_id' => $this->integer()->comment('id абонента (id клиента или id водителя)'),

            'call_from_operator' => $this->boolean()->comment('1 - звонок от оператора к абоненту, 0 - звонок от абонента к оператору'),

            'outgoing_time' => $this->integer()->comment('оператор взял трубку для дозвона до клиента'),
            'incoming_time' => $this->integer()->comment('у оператора зазвонил телефон при звонке от клиента'),
            'accepted_time' => $this->integer()->comment('приемник звонка взял трубку (если был исходящий от оператора звонок - то в этот момент клиент взял трубку, если звонок был от клиента, то оператор взял трубку)'),
            'completed_time' => $this->integer()->comment('звонок успешно завершен'),
            'canceled_time' => $this->integer()->comment('звонок сброшен или текущий оператор не взял трубку'),
        ]);
    }

    public function down()
    {
        $this->dropTable('call');
    }
}
