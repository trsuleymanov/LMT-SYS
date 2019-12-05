<?php

namespace app\models;

use Yii;


class CallEvent extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'call_event';
    }

    public function rules()
    {
        return [
            [['call_id', 'operator_user_id', 'event_time', /*'ping_at'*/ 'created_at'], 'integer'],
            [['operator_sip'], 'string', 'max' => 100],
            [['event'], 'string', 'max' => 50],
            [['ats_eventID'], 'string', 'max' => 36],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ats_eventID' => 'События id в АТС',
            'created_at' => 'Создан',
            'call_id' => 'ID звонка',
            'operator_sip' => 'Абонента логин в АТС',
            'operator_user_id' => 'Оператор',
            'event' => 'Событие',
            'event_time' => 'Время события',
            //'ping_at' => 'Время серверное когда пришел пинг'
        ];
    }


    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

//        $call = $this->call;
//
//        $operator_subscription = OperatorBeelineSubscription::find()
//            ->where(['mobile_ats_login' => $this->operator_sip])
//            ->one();
//
//        $aFields = [
//            'call_id' => $call->id,
//            'ats_callId' => $call->ext_tracking_id,
//            'ats_subscriptionId' => ($operator_subscription != null ? $operator_subscription->subscription_id : ''),
//            'event_name' => (isset(CallEvent::getEvents()[$this->event]) ? CallEvent::getEvents()[$this->event] : ''),
//            'ats_eventID' => $this->ats_eventID,
//            'crm_event_time' => $this->created_at,
//            'ats_event_time' => $this->event_time,
//            'call_type' => $call->call_direction,
//            'sip' => ($operator_subscription != null ? $operator_subscription->mobile_ats_login : ''),
//            'client_phone' => $call->operand,
//        ];
//        CallEvent::saveLog($aFields);
    }


    public function getCall()
    {
        return $this->hasOne(Call::className(), ['id' => 'call_id']);
    }


    public static function getEvents() {

        // события (встроены в код, поэтому встроены и в таблицу):
        // + input_call_created_by_client - поступил входящий звонок от клиента
        // + output_call_created_by_operator - был создан оператором исходящий звонок
        // + input_call_cancelled_by_operator - сброшен/отменен оператором входящий вызов - если до начала разговора пришли: internalReleaseCause=Busy, state=Released, releaseTime=1545883172368,
        // +/+ output_call_cancelled_by_operator - сброшен/отменен оператором исходящий вызов (свой вызов прервал)
        // + output_call_cancelled_by_client - сброшен/отменен клиентом исходящий вызов  (свой вызов прервал)
        // + input_call_accepted_by_operator - был принят оператором входящий вызов, начат разговор
        // + output_call_accepted_by_client - был принят клиентом исходящий вызов, начат разговор
        // +/+ call_finished_by_client - клиент положил трубку закончив разговор
        // +/+/+ call_finished_by_operator - оператор положил трубку закончив разговор
        // + operator_temporarily_unavailable
        // + call_from_ats_to_operator
        // + (output_call_accepted_by_operator ->) output_call_dial_to_client
        // + output_call_cancelled_by_ats

        return [
            // +
            'input_call_created_by_client' => 'Поступил входящий звонок от клиента',

            'output_call_created_by_operator' => 'Был создан оператором исходящий звонок',

            // не надо отправлять сигнал в браузер, т.к. оператор остается висеть на линии
            'input_call_cancelled_by_operator' => 'Сброшен/отменен оператором входящий вызов',

            //так как call_finished_by_client может происходить как после разговора, так и до, то ввожу
            //новое событие для завершения до разговора:
            'input_call_cancelled_by_client' => 'Отменен клиентом входящий вызов (свой вызов отменил)',

            'output_call_cancelled_by_operator' => 'Сброшен/отменен оператором исходящий вызов (свой вызов прервал)',

            'output_call_cancelled_by_client' => 'Сброшен/отменен клиентом исходящий вызов  (оператора вызов прервал)',


            // +
            'input_call_accepted_by_operator' => 'Был принят оператором входящий вызов, начат разговор',

            'output_call_accepted_by_client' => 'Был принят клиентом исходящий вызов, начат разговор',


            // +/?
            'call_finished_by_client' => 'Клиент положил трубку закончив разговор',

            // +/?
            'call_finished_by_operator' => 'Оператор положил трубку закончив разговор',

            // ?
            'operator_temporarily_unavailable' => 'Оператор временно недоступен',
            'call_from_ats_to_operator' => 'АТС звонит к оператору',
            'output_call_dial_to_client' => 'Был принят оператором исходящий вызов от АТС, начался дозвон до клиента',
            'output_call_cancelled_by_ats' => 'АТС не дождолась оператора и сбросила исходящий вызов',

            'server_failure' => 'Ошибка сервера, соединение остановлено',
        ];
    }


    // отправка сообщения в браузер с данными очередного события
    public function sendToBrawser() {

        if(empty($this->operator_user_id)) {
            return false;
        }

        $call = $this->call;

        $aUsersIds = [
            $this->operator_user_id
        ];
        $data = [
            'is_incoming_call' => ($call->call_direction == 'input'),
            'call_id' => $this->call_id,
            'call_status' => $call->status,
            'created_at' => date('H:i:s', $this->created_at),
            'event_name' => $this->event,
            'client_phone' => $call->operand,
            //'handling_call_operator_id' => $call->handling_call_operator_id,
            //'call_from_operator' => $call->call_from_operator
        ];

        if(in_array($this->event, ['call_finished_by_client', 'call_finished_by_operator'])) {
            $data['speaking_seconds'] = round(($call->ats_eok_time - $call->ats_answer_time)/1000, 0);
        }


        SocketDemon::sendOutBrowserMessageInstant('all_site_pages', [''], 'updateCall', $data, $aUsersIds);
        return true;

    }


    public static function saveLog($aFields) {

//        $aFields = [
//            'call_id' => '',
//            'ats_callId' => '',
//            'ats_subscriptionId' => '',
//            'event_name' => '',
//            'ats_eventID' => '',
//            'crm_event_time' => '',
//            'ats_event_time' => '',
//            'call_type' => '',
//            'sip' => '',
//            'client_phone' => '',
//        ];

//        Какие данные нужны:
//        - id звонка в CRM
//        - id звонка в АТС (поле extTrackingId)
//        - id подписки в АТС (поле subscriptionId)
//        - название события
//        - id события в АТС (поле eventID)
//        - время CRM - дд.мм.гггг ч.м.с - unixtime
//        - время АТС - дд.мм.гггг ч.м.с - unixtime с миллисекундами (это поля startTime/releaseTime)
//        - тип звонка: исходящий/входящий
//        - SIP оператора (в АТС поле userId)
//        - телефон клиента

        //return true;

        $last_symbol = substr($_SERVER['DOCUMENT_ROOT'], strlen($_SERVER['DOCUMENT_ROOT']) - 1, 1);
        if($last_symbol != '/') {
            $file = $_SERVER['DOCUMENT_ROOT'].'/../log/call_event.txt';
        }else {
            $file = $_SERVER['DOCUMENT_ROOT'].'../log/call_event.txt';
        }

        $fp = fopen($file, "a+");

        fwrite($fp, 'событие: '.$aFields['event_name']."\n");
        fwrite($fp, 'код события в АТС (eventID): '.$aFields['ats_eventID']."\n");
        fwrite($fp, 'id звонка в CRM: '.$aFields['call_id']."\n");
        fwrite($fp, 'id звонка в АТС (extTrackingId): '.$aFields['ats_callId']."\n");
        fwrite($fp, 'id подписки в АТС (subscriptionId): '.$aFields['ats_subscriptionId']."\n");
        fwrite($fp, 'время CRM                       : '.date('d.m.Y H:i:s', $aFields['crm_event_time']).' - '.$aFields['crm_event_time']."\n");

        if(!empty($aFields['ats_event_time'])) {
            fwrite($fp, 'время АТС(startTime/releaseTime): '.date('d.m.Y H:i:s', $aFields['ats_event_time']/1000).' - '.$aFields['ats_event_time']."\n");
        }else {
            fwrite($fp, 'время АТС(startTime/releaseTime): '."\n");
        }

        fwrite($fp, 'тип звонка: '.($aFields['call_type'] == 'input' ? 'Вх.' : 'Исх.')."\n");
        fwrite($fp, 'SIP (в АТС поле userId): '.$aFields['sip']."\n");
        fwrite($fp, 'телефон клиента: '.$aFields['client_phone']."\n\n");

        fclose($fp);
    }
}
