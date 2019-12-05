<?php
namespace app\modules\beeline\controllers;


use app\models\Call;
use app\models\CallEvent;
use app\models\Client;
use app\models\Driver;
use app\models\Order;
use app\models\SocketDemon;
use app\models\User;
use SimpleXMLElement;
use XMLReader;
use Yii;
use yii\base\ErrorException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;

/**
 * Единая входная точка для запросов от АТС билайна
 */
class DefaultController extends Controller
{
    public $modelClass = '';

    // если 30 секунд из АТС не приходят события по поводу
    // "не закрытого звонка", то звонок закрывается автоматически
    protected static $waiting_time_to_close_call = 30;



    public function actionIndex()
    {
        Yii::$app->response->format = 'json';

        $xmlstring = Yii::$app->request->getRawBody();

        $event = [];

        if(!empty($xmlstring)) {
            $XMLReader = new XMLReader();
            $XMLReader->xml($xmlstring);
            //var_dump($XMLReader->isValid());

            while ($XMLReader->read()) {

                if ($XMLReader->nodeType == XMLReader::ELEMENT) {
                    $prevLocalName = $XMLReader->localName;
                    continue;
                }
                if ($XMLReader->nodeType != XMLReader::TEXT) {
                    continue;
                }
                $event[$prevLocalName] = $XMLReader->value;
            }
        }

        if(!isset($event['extTrackingId'])) {
            return;
        }

//        $msg = '';
//        $msg .= 'время: '.date('H:i:s')."<br />";
//        foreach($event as $key => $val) {
//            $msg .= $key.'='.$val."<br />";
//        }
//        Yii::$app->mailer->compose()
//            ->setFrom('admin@developer.almobus.ru')
//            ->setTo('test.shetinin@gmail.com')
//            //->setTo('nara-dress@yandex.ru')
//            ->setSubject('сообщение от АТС')
//            //->setTextBody($msg)
//            ->setHtmlBody($msg)
//            ->send();
        //return;

        $call = Call::find()->where(['ext_tracking_id' => $event['extTrackingId']])->one();



        // входящий звонок от клиента или исходящий от оператора
        if($call == null) {

            // + Звонок от клиента
            if($event['personality'] == 'Terminator') {

                $call = new Call();
                $call->t_create = time();
                $call->ext_tracking_id = $event['extTrackingId']; // Код звонка в АТС
                if(strpos($event['address'], 'tel:+7') !== false) {
                    $call->operand = Client::convertMobilePhone(str_replace('tel:+7', '', $event['address']));
                }else {
                    if(isset($event['userDN'])) {
                        $aPhoneData = explode(';', $event['userDN']);
                        $call->operand = Client::convertMobilePhone(str_replace('tel:+7', '', $aPhoneData[0]));
                        $call->operand .= ' ('.str_replace('ext=', '', $aPhoneData[1]).')';
                    }else {
                        $call->operand = $event['address'];
                    }
                }
                $call->call_direction = 'input';
                $call->ats_start_time = $event['startTime'];
                //$call->sip = str_replace('@mpbx.sip.beeline.ru', '', $event['targetId']);

                $call->ats_eok_time = '';
                $call->t_hungup = time() + self::$waiting_time_to_close_call;
                $call->status = 'not_completed';
                if (!$call->save(false)) {
                    return;
                }

                $call_event = $call->createEvent($event['targetId'], 'input_call_created_by_client', $event['startTime'], $event['eventID']);

                // !!! Начался входящий дозвон от клиента
                if ($call_event != null) {
                    $call_event->sendToBrawser();

                    // обновляем в браузерах всех пользователей количество входящих
                    Call::sentToBrawsersIncomingCallsCount();

                    // отмечаем серым фоном заявку если по ней идет звонок
                    $call->updateIncomingRequestOrders();
                }

            // + Звонок от оператора
            }elseif($event['personality'] == 'Originator') {

                $call = new Call();
                $call->t_create = time();
                $call->ext_tracking_id = $event['extTrackingId'];
                if(strpos($event['address'], 'tel:+7') !== false) {
                    $call->operand = Client::convertMobilePhone(str_replace('tel:+7', '', $event['address']));
                }else {
                    if(isset($event['userDN'])) {
                        $aPhoneData = explode(';', $event['userDN']);
                        $call->operand = Client::convertMobilePhone(str_replace('tel:+7', '', $aPhoneData[0]));
                        $call->operand .= ' ('.str_replace('ext=', '', $aPhoneData[1]).')';
                    }else {
                        $call->operand = $event['address'];
                    }
                }

                $call->call_direction = 'output';
                $call->ats_start_time = $event['startTime'];
                //$call->sip = str_replace('@mpbx.sip.beeline.ru', '', $event['targetId']);

                $call->ats_eok_time = '';
                $call->t_hungup = time() + self::$waiting_time_to_close_call;
                $call->status = 'not_completed';
                if (!$call->save(false)) {
                    return;
                }

                $call_event = $call->createEvent($event['targetId'], 'output_call_created_by_operator', $event['startTime'], $event['eventID']);
                // отправил в браузер сигнал к вызвавшему звонок оператору что начат звонок
                if ($call_event != null) {

                    //$call->setField('handling_call_operator_id', $call_event->operator_user_id);

                    $call_event->sendToBrawser();

                    // отмечаем серым фоном заявку если по ней идет звонок
                    $call->updateIncomingRequestOrders();

                    // отправляем в браузер оператору окно звонка с данными клиента
                    $call->sendToBrawserCallWindow();
                }


            // + АТС звонит оператору
            }elseif($event['personality'] == 'Click-to-Dial') {

                // по сути это исходящий звонок от оператора к клиенту
                $call = new Call();
                $call->t_create = time();
                $call->ext_tracking_id = $event['extTrackingId'];
                // в этот момент в поле адреса содержиться номер оператора, а не клиента
                //$call->operand = Client::convertMobilePhone(str_replace('tel:+7', '', $event['address']));
                $call->call_direction = 'output';
                $call->ats_start_time = $event['startTime'];
                //$call->sip = str_replace('@mpbx.sip.beeline.ru', '', $event['targetId']);

                $call->ats_eok_time = '';
                $call->t_hungup = time() + self::$waiting_time_to_close_call;
                $call->status = 'not_completed';
                if (!$call->save(false)) {
                    return;
                }

                $call_event = $call->createEvent($event['targetId'], 'call_from_ats_to_operator', $event['startTime'], $event['eventID']);
                // отправка в браузер сигнал к вызвавшему звонок оператору что начат звонок
                if ($call_event != null) {
                    $call->setField('handling_call_operator_id', $call_event->operator_user_id);
                    $call->setField('sip', $event['targetId']);

                    $call_event->sendToBrawser();

                    // отправляем в браузер оператору окно звонка с данными клиента
                    $call->handling_call_operator_id = $call_event->operator_user_id;
                    //$call->sendToBrawserCallWindow();
                }
            }

        }
        else {


            // входящий либо исходящий звонок - дописывание данных
            if($call->call_direction == 'input')
            {
                // +++разговор окончен - клиент положил трубку
                if(isset($event['releasingParty']) && $event['releasingParty'] == 'remoteRelease')
                {
                    if(!isset($event['answerTime'])) {
                        // если время releaseTime равно времени 18 секунд +/- 0.03 секунды, то это
                        // событие не считаю событием окончания звонка.
                        // но через 18 секунд если не придут события, звонок надо закрывать "задним числом - минус 18 секунд"
                        $start_event = CallEvent::find()
                            ->where(['call_id' => $call->id])
                            ->andWhere(['operator_sip' => $event['targetId']])
                            ->orderBy(['id' => SORT_ASC])
                            ->one();
                        if ($start_event == null) {
                            return;
                        }
                        $check_time = $start_event->event_time + ($event['waitTime'] + 18) * 1000;
                        $deviation = 100;
                        if (
                            $check_time - $deviation < $event['releaseTime']
                            && $check_time + $deviation > $event['releaseTime']
                        ) {
                            return; // пропускаем такое событие "обрыва связи"
                        }
                    }


                    if(isset($event['answerTime'])) {

                        $call_event = $call->createEvent($event['targetId'], 'call_finished_by_client', $event['releaseTime'], $event['eventID']);


                        $call->ats_eok_time = $event['releaseTime'];
                        $call->t_hungup = time();

                        // устанавливаем статус звонку successfully_completed или quickly_completed
                        if(empty($call->t_answer)) { // не понятно может ли такой случай быть, но пускай будет обработка
                            $call->status = 'not_completed';
                        }else if(time() - $call->t_answer <= 3) {
                            $call->status = 'quickly_completed';
                        }else {
                            $call->status = 'successfully_completed';
                        }

                        $call->save(false);


                        // создаем/изменяем обращения в момент завершения звонка
                        $call->createUpdateCase();


                        if($call->status == 'not_completed') {
                            // отправляем сигнал в браузеры с новым количеством пропущенных звонков
                            Call::sentToBrawsersMissedCallsCount();
                        }

                        // отправляем сигнал в браузер что соединение завершено
                        if ($call_event != null) {
                            $call_event->sendToBrawser();

                            // обновляем в браузерах всех пользователей количество входящих
                            Call::sentToBrawsersIncomingCallsCount();
                        }

                    }else {

                        $call_event = $call->createEvent($event['targetId'], 'input_call_cancelled_by_client', $event['releaseTime'], $event['eventID']);

                        $call->ats_eok_time = $event['releaseTime'];
                        $call->t_hungup = time();
                        $call->status = 'not_completed';
                        $call->save(false);

                        // создаем/изменяем обращения в момент завершения звонка
                        $call->createUpdateCase();

                        // отправляем сигнал в браузеры с новым количеством пропущенных звонков
                        Call::sentToBrawsersMissedCallsCount();

                        // обновляем в браузерах всех пользователей количество входящих
                        Call::sentToBrawsersIncomingCallsCount();

                        // отправляем сигнал в браузер что соединение завершено
                        if ($call_event != null) {
                            $call_event->sendToBrawser();
                        }
                    }

                // +++оператор положил трубку закончив разговор
                }elseif(isset($event['releasingParty']) && $event['releasingParty'] == 'localRelease')
                {
                    $call_event = $call->createEvent($event['targetId'], 'call_finished_by_operator', $event['releaseTime'], $event['eventID']);

                    $call->ats_eok_time = $event['releaseTime'];
                    $call->t_hungup = time();

                    // устанавливаем статус звонку successfully_completed или quickly_completed
                    if(empty($call->t_answer)) {
                        $call->status = 'not_completed';
                    }else if(time() - $call->t_answer <= 3) {
                        $call->status = 'quickly_completed';
                    }else {
                        $call->status = 'successfully_completed';
                    }

                    $call->save(false);

                    // создаем/изменяем обращения в момент завершения звонка
                    $call->createUpdateCase();

                    if($call->status == 'not_completed') {
                        // отправляем сигнал в браузеры с новым количеством пропущенных звонков
                        Call::sentToBrawsersMissedCallsCount();
                    }

                    // отправляем сигнал в браузер что соединение завершено
                    if ($call_event != null) {
                        $call_event->sendToBrawser();
                    }

                // +начало разговора состоялось
                }elseif(isset($event['answerTime']))
                {
                    $call_event = $call->createEvent($event['targetId'], 'input_call_accepted_by_operator', $event['answerTime'], $event['eventID']);

                    if(empty($call->t_answer)) {
                        $call->t_answer = time();
                        $call->ats_answer_time = $event['answerTime'];
                        $call->ats_eok_time = '';
                        $call->t_hungup = '';
                        $call->status = '';

                        $call->save(false);
                    }

                    // уходит сигнал в браузер о том что разговор начался
                    if ($call_event != null) {

                        $call->setField('handling_call_operator_id', $call_event->operator_user_id);
                        $call->setField('sip', $event['targetId']);

                        // обновляем в браузерах всех пользователей количество входящих
                        Call::sentToBrawsersIncomingCallsCount();

                        // отправляем в браузер оператору принявшему вызов сигнал, чтобы появился красный кружок
                        //$call->sentToBrawserActiveCalling(true);

                        $call_event->sendToBrawser();

                        // отправляем в браузер оператору окно звонка с данными клиента
                        $call->handling_call_operator_id = $call_event->operator_user_id;
                        $call->sendToBrawserCallWindow();
                    }

                }else { // до начала разговора

                    // + оператор временно недоступен
                    if(isset($event['internalReleaseCause']) && $event['internalReleaseCause'] == 'Temporarily Unavailable') {

                        $call->createEvent($event['targetId'], 'operator_temporarily_unavailable', $event['releaseTime'], $event['eventID']);

                        // буду "завершать" звонок на каждом недоступном оператора и возобновлять звонок на каждом новом сигнале от АТС
                        $call->ats_eok_time = '';
                        $call->t_hungup = time() + self::$waiting_time_to_close_call;
                        $call->status = 'not_completed';
                        if (!$call->save(false)) {
                            return;
                        }


                    // + сброшен/отменен оператором входящий вызов - в реальности событие ничего не меняет / не происходит ничего
                    }elseif(isset($event['internalReleaseCause']) && $event['internalReleaseCause'] == 'Busy')
                    {
                        $call_event = $call->createEvent($event['targetId'], 'input_call_cancelled_by_operator', $event['releaseTime'], $event['eventID']);

                        $call->ats_eok_time = '';
                        $call->t_hungup = time() + self::$waiting_time_to_close_call;
                        $call->status = 'not_completed';
                        if (!$call->save(false)) {
                            return;
                        }

                        if ($call_event != null) {
                            $call_event->sendToBrawser();
                        }

                        // обновляем в браузерах всех пользователей количество входящих
                        Call::sentToBrawsersIncomingCallsCount();


                    }else {
                        // + звонок - входящий, разговор еще не начинался, сброса не было
                        if(isset($event['state']) && $event['state'] == 'Alerting') {
                            $call_event = $call->createEvent($event['targetId'], 'input_call_created_by_client', $event['startTime'], $event['eventID']);
                            // отправляем в браузер сигнал что к оператору поступает входящий
                            // а уже браузер решит что делать...
                            if ($call_event != null) {

                                $call->ats_eok_time = 0;
                                $call->t_hungup = time() + self::$waiting_time_to_close_call;
                                $call->status = 'not_completed';
                                if (!$call->save(false)) {
                                    return;
                                }

                                $call_event->sendToBrawser();

                                // обновляем в браузерах всех пользователей количество входящих
                                Call::sentToBrawsersIncomingCallsCount();
                            }
                        }
                    }
                }


            }
            else // звонок исходящий от оператора к клиенту !
            {
                // +++связь окончена - оператор "окончательно" положил трубку
                if(isset($event['releasingParty']) && $event['releasingParty'] == 'remoteRelease' && $event['personality'] == 'Originator')
                {
                    $call_event = $call->createEvent($event['targetId'], 'call_finished_by_operator', $event['releaseTime'], $event['eventID']);

                    $call->ats_eok_time = $event['releaseTime'];
                    $call->t_hungup = time();

                    // устанавливаем статус звонку successfully_completed или quickly_completed
                    if (empty($call->t_answer)) {
                        $call->status = 'not_completed';
                    } else if (time() - $call->t_answer <= 3) {
                        $call->status = 'quickly_completed';
                    } else {
                        $call->status = 'successfully_completed';
                    }

                    if (!$call->save(false)) {
                        return;
                    }

                    // создаем/изменяем обращения в момент завершения звонка
                    $call->createUpdateCase();

                    // отправляем сигнал в браузер что соединение завершено
                    if ($call_event != null) {
                        $call_event->sendToBrawser();
                    }


                    // +++связь окончена - клиент положил трубку
                }elseif(isset($event['releasingParty']) && $event['releasingParty'] == 'remoteRelease' && $event['personality'] == 'Click-to-Dial')
                {
                    $call_event = $call->createEvent($event['targetId'], 'call_finished_by_client', $event['releaseTime'], $event['eventID']);

                    $call->ats_eok_time = $event['releaseTime'];
                    $call->t_hungup = time();

                    // устанавливаем статус звонку successfully_completed или quickly_completed
                    if (empty($call->t_answer)) {
                        $call->status = 'not_completed';
                    } else if (time() - $call->t_answer <= 3) {
                        $call->status = 'quickly_completed';
                    } else {
                        $call->status = 'successfully_completed';
                    }

                    if (!$call->save(false)) {
                        return;
                    }

                    // создаем/изменяем обращения в момент завершения звонка
                    $call->createUpdateCase();

                    // отправляем сигнал в браузер что соединение завершено
                    if ($call_event != null) {
                        $call_event->sendToBrawser();
                    }


                // +++связь окончена - оператор положил трубку
                }elseif(isset($event['releasingParty']) && $event['releasingParty'] == 'localRelease') {

                    $call_event = $call->createEvent($event['targetId'], 'call_finished_by_operator', $event['releaseTime'], $event['eventID']);

                    $call->ats_eok_time = $event['releaseTime'];
                    $call->t_hungup = time();

                    // устанавливаем статус звонку successfully_completed или quickly_completed
                    if(empty($call->t_answer)) {
                        $call->status = 'not_completed';
                    }else if(time() - $call->t_answer <= 3) {
                        $call->status = 'quickly_completed';
                    }else {
                        $call->status = 'successfully_completed';
                    }

                    if (!$call->save(false)) {
                        return;
                    }

                    // создаем/изменяем обращения в момент завершения звонка
                    $call->createUpdateCase();

                    // отправляем сигнал в браузер что соединение завершено
                    if ($call_event != null) {
                        $call_event->sendToBrawser();
                    }


                // +++оператор сам взял свой вызов, тем самым сбросив вызов - это глюк возможный  с помощью Liphone - при таком
                // событии приходят такие же данные как при событии
                // + клиент взял трубку - начало разговора состоялось
                }elseif(isset($event['answerTime']) && $event['personality'] == 'Originator') {

                    $call->t_answer = time();
                    $call->ats_answer_time = $event['answerTime'];
                    $call->ats_eok_time = '';
                    $call->t_hungup = '';
                    $call->status = '';
                    if (!$call->save(false)) {
                        return;
                    }

                    $call_event = $call->createEvent($event['targetId'], 'output_call_accepted_by_client', $event['answerTime'], $event['eventID']);
                    // оператор не дождался клиента и сбросил исходящий вызов
                    if ($call_event != null) {
                        $call_event->sendToBrawser();

                        Call::sentToBrawsersIncomingCallsCount();
                    }

                // +начало разговора состоялось
//                }elseif(isset($event['answerTime']) && $event['personality'] == 'Click-to-Dial') {
//
//                    $call_event = $call->createEvent($event['targetId'], 'output_call_accepted_by_client', $event['answerTime'], $event['eventID']);
//
//                    if(empty($call->t_answer)) {
//                        $call->t_answer = time();
//                        $call->ats_answer_time = $event['answerTime'];
//                        $call->ats_eok_time = '';
//                        $call->t_hungup = '';
//                        $call->status = '';
//                        if (!$call->save(false)) {
//                            return;
//                        }
//                    }
//
//                    // уходит сигнал в браузер о том что разговор начался
//                    if ($call_event != null) {
//                        $call_event->sendToBrawser();
//
//                        // обновляем в браузерах всех пользователей количество входящих
////                        Call::sentToBrawsersIncomingCallsCount();
//                    }

                }else { // до начала разговора

                    // +++АТС не дождолась оператора и сбросила вызов
                    if(
                        isset($event['cdrTerminationCause']) && $event['cdrTerminationCause'] == '111'
                        && isset($event['internalReleaseCause']) && $event['internalReleaseCause'] == 'Request Timeout'
                        && $event['personality'] == 'Click-to-Dial'
                    ) {
                        $call->ats_eok_time = $event['releaseTime'];
                        $call->t_hungup = time();
                        $call->status = 'not_completed';
                        if (!$call->save(false)) {
                            return;
                        }

                        // создаем/изменяем обращения в момент завершения звонка
                        $call->createUpdateCase();

                        $call_event = $call->createEvent($event['targetId'], 'output_call_cancelled_by_ats', $event['releaseTime'], $event['eventID']);
                        if ($call_event != null) {
                            $call_event->sendToBrawser();
                        }

                    // случай генерируемый программой Тимура - отмена исходящего вызова
                    }elseif(
                            isset($event['cdrTerminationCause']) && $event['cdrTerminationCause'] == '111'
                            && isset($event['internalReleaseCause']) && $event['internalReleaseCause'] == 'Forbidden'
                            && $event['personality'] == 'Click-to-Dial'
                        ) {

                        $call->ats_eok_time = $event['releaseTime'];
                        $call->t_hungup = time();
                        $call->status = 'not_completed';
                        if (!$call->save(false)) {
                            return;
                        }

                        // создаем/изменяем обращения в момент завершения звонка
                        $call->createUpdateCase();

                        $call_event = $call->createEvent($event['targetId'], 'output_call_cancelled_by_operator', $event['releaseTime'], $event['eventID']);
                        // был отменен звонок от АТС к оператору оператором
                        if ($call_event != null) {
                            $call_event->sendToBrawser();
                        }


                    // +++сброс клиентом
                    }elseif(
                        isset($event['internalReleaseCause']) && $event['internalReleaseCause'] == 'Busy'
                        && $event['personality'] == 'Originator'
                        && isset($event['state']) && $event['state'] == 'Active'
                    ) { // при звонке от оператора к клиенту, клиент сбросил, но оператор продолжает висеть

                        $call->ats_eok_time = $event['startTime'];
                        $call->t_hungup = time();
                        $call->status = 'not_completed';
                        if (!$call->save(false)) {
                            return;
                        }

                        // отправляем сигнал в браузеры с новым количеством пропущенных звонков
//                        Call::sentToBrawsersMissedCallsCount();
//                        Call::sentToBrawsersIncomingCallsCount();

                        $call_event = $call->createEvent($event['targetId'], 'output_call_cancelled_by_client', $event['startTime'], $event['eventID']);
                        if ($call_event != null) {
                            $call_event->sendToBrawser();
                        }


                    // +++сброс оператором
                    }elseif(
                        isset($event['internalReleaseCause']) && $event['internalReleaseCause'] == 'Busy'
                        && $event['personality'] == 'Originator'
                        && isset($event['state']) && $event['state'] == 'Released'
                    ) { // при звонке от оператора к клиенту, клиент вначале положил трубку, а затем оператор положил трубку (т.к. продолжал висеть на линии)

                        $call->ats_eok_time = $event['releaseTime'];
                        $call->t_hungup = time();
                        $call->status = 'not_completed';
                        if (!$call->save(false)) {
                            return;
                        }

                        // создаем/изменяем обращения в момент завершения звонка
                        $call->createUpdateCase();

                        $call_event = $call->createEvent($event['targetId'], 'output_call_cancelled_by_operator', $event['releaseTime'], $event['eventID']);
                        // оператор не дождался клиента и сбросил исходящий вызов
                        if ($call_event != null) {
                            $call_event->sendToBrawser();
                        }


                    // +++значит был сброшен звонок от АТС к оператору оператором
                    }elseif(
                        isset($event['internalReleaseCause']) && $event['internalReleaseCause'] == 'Busy'
                        && $event['personality'] == 'Click-to-Dial'
                    ) {
                        $call->ats_eok_time = $event['releaseTime'];
                        $call->t_hungup = time();
                        $call->status = 'not_completed';
                        if (!$call->save(false)) {
                            return;
                        }

                        // создаем/изменяем обращения в момент завершения звонка
                        $call->createUpdateCase();

                        $call_event = $call->createEvent($event['targetId'], 'output_call_cancelled_by_operator', $event['releaseTime'], $event['eventID']);
                        // был отменен звонок от АТС к оператору оператором
                        if ($call_event != null) {
                            $call_event->sendToBrawser();
                        }


                    // а разве это событие когда-нибудь может случиться, ведь выше уже стоит проверка на
                    //  isset($event['releasingParty']) && $event['releasingParty'] == 'localRelease'
//                    }elseif( // сброс оператором
//                        isset($event['releasingParty']) && $event['releasingParty'] == 'localRelease'
//                        && $event['personality'] == 'Originator'
//                        && isset($event['state']) && $event['state'] == 'Released'
//                    ) { // при звонке от оператора к клиенту, оператор не дождался клиента и сбросил
//
//                        $call_event = $call->createEvent($event['targetId'], 'output_call_cancelled_by_operator', $event['releaseTime'], $event['eventID']);
//                        // оператор сбросил исходящий вызов
//                        if ($call_event != null) {
//                            $call_event->sendToBrawser();
//                        }
//
//                        $call->setField('ats_eok_time', $event['releaseTime']);
//                        $call->setField('t_hungup', time());
//                        $call->setField('status', 'not_completed');
//
//                        // отправляем сигнал в браузеры с новым количеством пропущенных звонков
//                        Call::sentToBrawsersMissedCallsCount();


                    }elseif(
                        isset($event['internalReleaseCause']) && $event['internalReleaseCause'] == 'Busy'
                        && $event['personality'] == 'Terminator'
                    ) {
/*
                        $msg = '';
                        $msg .= 'время: '.date('H:i:s')."<br />";
                        foreach($event as $key => $val) {
                            $msg .= $key.'='.$val."<br />";
                        }
                        Yii::$app->mailer->compose()
                            ->setFrom('admin@developer.almobus.ru')
                            ->setTo('test.shetinin@gmail.com')
                            //->setTo('nara-dress@yandex.ru')
                            ->setSubject('Не было сохранено событие!!!')
                            //->setTextBody($msg)
                            ->setHtmlBody($msg)
                            ->send();
*/
                    }else {

                        // + звонок - исходящий, разговор еще не начинался, сброса не было
                        if(isset($event['state']) && $event['state'] == 'Alerting') {

                            if (empty($call->operand)) {
                                //$call->operand = Client::convertMobilePhone(str_replace('tel:+7', '', $event['address']));

                                if(strpos($event['address'], 'tel:+7') !== false) {
                                    $call->operand = Client::convertMobilePhone(str_replace('tel:+7', '', $event['address']));
                                }else {
                                    if(isset($event['userDN'])) {
                                        $aPhoneData = explode(';', $event['userDN']);
                                        $call->operand = Client::convertMobilePhone(str_replace('tel:+7', '', $aPhoneData[0]));
                                        $call->operand .= ' ('.str_replace('ext=', '', $aPhoneData[1]).')';
                                    }else {
                                        $call->operand = $event['address'];
                                    }
                                }

                            }

                            if ($event['personality'] == 'Click-to-Dial') {

                                if (empty($call->ats_start_time)) {
                                    $call->ats_start_time = $event['startTime'];
                                }

                                $call_event = $call->createEvent($event['targetId'], 'call_from_ats_to_operator', $event['startTime'], $event['eventID']);

                            } elseif ($event['personality'] == 'Originator') {

                                // этих событий может быть много
                                $call_event = $call->createEvent($event['targetId'], 'output_call_dial_to_client', $event['startTime'], $event['eventID']);
                            }

                            $call->ats_eok_time = '';
                            $call->t_hungup = time() + self::$waiting_time_to_close_call;
                            $call->status = 'not_completed';
                            if($call_event->event == 'call_from_ats_to_operator') {
                                $call->handling_call_operator_id = $call_event->operator_user_id;
                                $call->sip = $event['targetId'];
                            }
                            if (!$call->save(false)) {
                                return;
                            }

                            // отправляем в браузер сигнал что к оператору поступает входящий
                            if ($call_event != null) {
                                $call_event->sendToBrawser();

                                // отправляем в браузер оператору окно звонка с данными клиента
                                if($call_event->event == 'call_from_ats_to_operator') {
                                    //$call->sendToBrawserCallWindow();
                                }
                            }


                        // +++server_failure - Ошибка сервера, соединение остановлено
                        }elseif(isset($event['internalReleaseCause']) && $event['internalReleaseCause'] == 'Server Failure') {

                            $call->ats_eok_time = $event['releaseTime'];
                            $call->t_hungup = time();
                            $call->status = 'not_completed';
                            if (!$call->save(false)) {
                                return;
                            }

                            // создаем/изменяем обращения в момент завершения звонка
                            $call->createUpdateCase();

                            $call_event = $call->createEvent($event['targetId'], 'server_failure', $event['releaseTime'], $event['eventID']);
                            if ($call_event != null) {
                                $call_event->sendToBrawser();
                            }


                        }else {
/*
                            // не нашлось события для условия:
                            $msg = '';
                            $msg .= 'время: '.date('H:i:s')."<br />";
                            foreach($event as $key => $val) {
                                $msg .= $key.'='.$val."<br />";
                            }
                            Yii::$app->mailer->compose()
                                ->setFrom('admin@developer.almobus.ru')
                                ->setTo('test.shetinin@gmail.com')
                                //->setTo('nara-dress@yandex.ru')
                                ->setSubject('Не было сохранено событие!!!')
                                //->setTextBody($msg)
                                ->setHtmlBody($msg)
                                ->send();
*/
                        }
                    }
                }
            }

        }
    }

}
