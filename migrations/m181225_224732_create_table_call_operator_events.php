<?php

use yii\db\Migration;

/**
 * Class m181225_224732_create_table_call_operator_events
 */
class m181225_224732_create_table_call_operator_events extends Migration
{
    public function up()
    {
//        $this->createTable('call_operator_events', [
//            'id' => $this->primaryKey(),
//            'call_id' => $this->integer()->comment('ID звонка'),
//            'operator_sip' => $this->string(100)->comment('Абонента логин в АТС'),
//            'operator_user_id' => $this->integer()->comment('Оператор'),
//            'call_from_operator' => $this->boolean()->comment('1 - звонок от оператора к абоненту, 0 - звонок от абонента к оператору'),
//            'calling_start_at' => $this->integer()->comment('Начало исходящего звонка, или начало прихода на АТС звонка входящего от клиента'),
//            'canceled_calling_at' => $this->integer()->comment('Время сброса соединения до начала разговора'),
//            'canceled_calling_by_operator' => $this->boolean()->comment('1 - звонок сбросил оператор, 0 - звонок сбросил клиент'),
//            'answer_start_at' => $this->integer()->comment('Время начала разговора'),
//            'finish_answer_at' => $this->integer()->comment('Время окончания разговора'),
//            'finish_by_operator' => $this->boolean()->comment('1 - положил трубку первым оператор, 0 - положил трубку первым клиент'),
//        ]);

        // события (встроены в код, поэтому встроены и в таблицу):
        // - input_call_created_by_client - поступил входящий звонок от клиента
        // - output_call_created_by_operator - был создан оператором исходящий звонок
        // - input_call_cancelled_by_operator - сброшен/отменен оператором входящий вызов - если до начала разговора пришли: internalReleaseCause=Busy, state=Released, releaseTime=1545883172368,
        //      но отсутствует releasingParty, а когда придет releasingParty=remoteRelease, значит клиент положил трубку
        // - output_call_cancelled_by_operator - сброшен/отменен оператором исходящий вызов (свой вызов прервал)
        // - output_call_cancelled_by_client - сброшен/отменен клиентом исходящий вызов  (свой вызов прервал)
        // - input_call_accepted_by_operator - был принят оператором входящий вызов, начат разговор
        // - output_call_accepted_by_client был принят клиентом исходящий вызов, начат разговор
        // - call_finished_by_client - клиент положил трубку закончив разговор
        // - call_finished_by_operator - оператор положил трубку закончив разговор

        $this->createTable('call_event', [
            'id' => $this->primaryKey(),
            'call_id' => $this->integer()->comment('ID звонка'),
            'operator_sip' => $this->string(100)->comment('Абонента логин в АТС'),
            'operator_user_id' => $this->integer()->comment('Оператор'),
            //'call_from_operator' => $this->boolean()->comment('1 - звонок от оператора к абоненту, 0 - звонок от абонента к оператору'),
            //'calling_start_at' => $this->integer()->comment('Начало исходящего звонка, или начало прихода на АТС звонка входящего от клиента'),
            //'canceled_calling_at' => $this->integer()->comment('Время сброса соединения до начала разговора'),
            //'canceled_calling_by_operator' => $this->boolean()->comment('1 - звонок сбросил оператор, 0 - звонок сбросил клиент'),
            //'answer_start_at' => $this->integer()->comment('Время начала разговора'),
            //'finish_answer_at' => $this->integer()->comment('Время окончания разговора'),
            //'finish_by_operator' => $this->boolean()->comment('1 - положил трубку первым оператор, 0 - положил трубку первым клиент'),
            'event' => $this->string(50)->comment('Событие'),
            'event_time' => $this->bigInteger()->comment('Время события'),
        ]);
    }

    public function down()
    {
        $this->dropTable('call_event');
    }
}
