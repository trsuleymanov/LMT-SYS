<?php

use yii\db\Migration;

/**
 * Class m190203_120716_change_table_call
 */
class m190203_120716_change_table_call extends Migration
{
    public function up()
    {
        $this->dropTable('call');
        $this->truncateTable('call_event');
        $this->truncateTable('call_appeal');
        $this->truncateTable('call_contact');

        $this->createTable('call', [
            'id' => $this->primaryKey(),
            'call_direction' => "ENUM('input', 'output')",
            'operand' => $this->string(20)->comment('Номер операнда (телефон клиента)'),
            't_create' => $this->integer()->comment('Создан'),
            't_answer' => $this->integer()->comment('Начало разговора'),
            't_hungup' => $this->integer()->comment('Окончание звонка'),

            'ats_start_time' => $this->bigInteger()->comment('Время начала соединения'),
            'ats_answer_time' => $this->bigInteger()->comment('Начало разговора (по версии АТС)'),
            'ats_eok_time' => $this->bigInteger()->comment('Время окончания связи'),
            'ext_tracking_id' => $this->string(12)->comment('Код звонка в АТС - extTrackingId')->unique(),

            'sip' => $this->string(100)->comment('SIP-аккаунт (логин в АТС оператора принявшего/создавшего вызов)'),
            'handling_call_operator_id' => $this->integer()->comment('Оператор (пользователь) принявший/создавший вызов'),
            'status' => "ENUM('not_completed', 'quickly_completed', 'successfully_completed')",
        ]);
    }

    public function down()
    {
        $this->dropTable('call');
        $this->createTable('call', [
            'id' => $this->primaryKey(),
            'call_contact_id' => $this->integer()->comment('Контакт'),
            'created_at' => $this->integer()->comment('Создан'),
            'answered_at' => $this->integer()->comment('Начало разговора'),
            'finished_at' => $this->integer()->comment('Окончание звонка'),
            'ext_tracking_id' => $this->string(12)->comment('Код звонка в АТС - extTrackingId')->unique(),
            'client_phone' => $this->string(20)->comment('Телефон клиента'),
            'handling_call_operator_id' => $this->integer()->comment('Оператор (пользователь) принявший/создавший вызов'),
            'call_from_operator' => $this->boolean()->comment('1 - звонок от оператора к абоненту, 0 - звонок от абонента к оператору'),

            'ats_start_time' => $this->bigInteger()->comment('Время начала соединения'),
            'ats_answer_time' => $this->bigInteger()->comment('Начало разговора (по версии АТС)'),
            'ats_finish_time' => $this->bigInteger()->comment('Время окончания связи'),
            'status' => $this->string(50)->comment('Статус'),
        ]);
    }
}
