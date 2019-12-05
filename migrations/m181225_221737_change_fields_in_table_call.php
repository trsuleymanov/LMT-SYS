<?php

use yii\db\Migration;

/**
 * Class m181225_221737_change_fields_in_table_call
 */
class m181225_221737_change_fields_in_table_call extends Migration
{
    public function up()
    {
        $this->dropTable('call');

        $this->createTable('call', [
            'id' => $this->primaryKey(),
            'created_at' => $this->integer()->comment('Создан'),
            'ext_tracking_id' => $this->string(12)->comment('Код звонка в АТС - extTrackingId')->unique(),
            'client_phone' => $this->string(20)->comment('Телефон клиента'),
            'call_from_operator' => $this->boolean()->comment('1 - звонок от оператора к абоненту, 0 - звонок от абонента к оператору'),

            'ats_start_time' => $this->bigInteger()->comment('Время начала соединения'),
            //'ats_answer_time' => $this->integer()->comment('Время начала разговора'),
            'ats_finish_time' => $this->bigInteger()->comment('Время окончания связи'),
            'status' => $this->string(50)->comment('Статус'),
        ]);
    }

    public function down()
    {
        $this->dropTable('call');
        $this->createTable('call', [
            'id' => $this->primaryKey(),
            'created_at' => $this->integer()->comment('Создан'),

            // записываетс при первом создании записи call
            'ext_tracking_id' => $this->string(12)->comment('Код звонка в АТС - extTrackingId')->unique(),

            // записываем при первом редактировании
            'ats_user_id' => $this->string(100)->comment('Абонента логин в АТС'),

            //  address=tel:+79625559569 - нужно убрать tel: и конвертировать номер в формат "+7-904-678-4559"
            'client_phone' => $this->string(20)->comment('Телефон клиента'),
            // находиться клиент с таким номером, если его нет, то не записываем это поле.
            'client_id' => $this->integer()->comment('Клиент'),

            // - если поле personality = Terminator, то звонит клиент оператору, если поле personality="Click-to-Dial", то звонит оператор клиенту.
            'call_from_operator' => $this->boolean()->comment('1 - звонок от оператора к абоненту, 0 - звонок от абонента к оператору'),

            // записывается при создании записи call
            'ats_start_time' => $this->integer()->comment('Время начала соединения'),

            // записывается когда приходит поле state=Active и есть поле answerTime
            'ats_answer_time' => $this->integer()->comment('Время начала разговора'),
            // записывается когда приходит state=Released
            'ats_release_time' => $this->integer()->comment('Время'),
        ]);
    }
}
