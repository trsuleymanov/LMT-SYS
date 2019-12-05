<?php

use yii\db\Migration;

/**
 * Class m190203_124205_change_table_call_appeal_to_call_case
 */
class m190203_124205_change_table_call_appeal_to_call_case extends Migration
{
    public function up()
    {
        $this->dropTable('call_appeal');

        $this->createTable('call_case', [
            'id' => $this->primaryKey(),
            'case_type' => "ENUM('administrative_request', 'information_request', 'operation_with_order','missed')",
            'order_id' => $this->integer()->comment('Заказ к которому относиться обращение'),
            'open_time' => $this->integer()->comment('Время поступления первого звонка по обращению'),
            'operand' => $this->string(20)->comment('Номер операнда (телефон клиента)'),
            'call_count' => $this->smallInteger()->comment('Количество звонков'),
            'status' => "ENUM('not_completed', 'adm_completed', 'inf_completed', 'missed_completed', 'input_call_missed_completed', 'output_call_missed_completed', 'auto_completed', 'inf_abnormal_call_completed', 'completed_by_trip_sending')",
            'close_time' => $this->integer()->comment('Время закрытия'),
        ]);
    }

    public function down()
    {
        $this->dropTable('call_case');
        $this->createTable('call_appeal', [
            'id' => $this->primaryKey(),
            'created_at' => $this->integer()->comment('Время создания'),
            'type' => "ENUM('administrative_request', 'information_request', 'operation_with_order')",
            'contact_id' => $this->integer()->comment('Контакт'),
            'success_call_id' => $this->integer()->comment('Звонок, инициировавший обращение'),
            'client_phone' => $this->string(20)->comment('Телефон клиента'),
            'operator_user_id' => $this->integer()->comment('Оператор, принявший звонок'),
        ]);
    }

}
