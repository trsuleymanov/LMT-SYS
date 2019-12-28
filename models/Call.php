<?php

namespace app\models;

use app\widgets\IncomingOrdersWidget;
use Yii;
use yii\base\ErrorException;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\ForbiddenHttpException;

/**
 * This is the model class for table "call".
 *
 * @property int $id
 * @property string $call_direction
 * @property string $operand Номер операнда (телефон клиента)
 * @property int $t_create Создан
 * @property int $t_answer Начало разговора
 * @property int $t_hungup Окончание звонка
 * @property int $ats_start_time Время начала соединения
 * @property int $ats_answer_time Начало разговора (по версии АТС)
 * @property int $ats_eok_time Время окончания связи
 * @property string $ext_tracking_id Код звонка в АТС - extTrackingId
 * @property string $sip SIP-аккаунт (логин в АТС)
 * @property int $handling_call_operator_id Оператор (пользователь) принявший/создавший вызов
 * @property string $status
 */
class Call extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'call';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['call_direction', 'status'], 'string'],
            [['t_create', 't_answer', 't_hungup', 'ats_start_time', 'ats_answer_time', 'ats_eok_time',
                'handling_call_operator_id', 'caused_by_missed_call_window'], 'integer'],
            [['operand'], 'string', 'max' => 20],
            [['ext_tracking_id'], 'string', 'max' => 12],
            [['sip'], 'string', 'max' => 100],
            [['ext_tracking_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'call_direction' => 'Направление звонка',
            'caused_by_missed_call_window' => 'Звонок был вызван из окна пропущенных звонков',
            'operand' => 'Номер операнда', // телефон клиента
            't_create' => 'Создан',
            't_answer' => 'Начало разговора',
            't_hungup' => 'Окончание звонка',
            'ats_start_time' => 'Время начала соединения',
            'ats_answer_time' => 'Начало разговора (по версии АТС)',
            'ats_eok_time' => 'Время окончания связи',
            'ext_tracking_id' => 'Код звонка в АТС - extTrackingId',
            'sip' => 'SIP-аккаунт оператора', // логин в АТС
            'handling_call_operator_id' => 'Оператор (пользователь) принявший/создавший вызов',
            'status' => 'Статус',
        ];
    }


    public static function sendErrorEmailToAdmin($theme, $msg) {

        Yii::$app->mailer->compose()
            ->setFrom('admin@developer.almobus.ru')
            ->setTo('vlad.shetinin@gmail.com')
            //->setTo('nara-dress@yandex.ru')
            ->setSubject($theme)
            //->setTextBody($msg)
            ->setHtmlBody($msg)
            ->send();
    }


    public function setField($field_name, $field_value)
    {
        if(!empty($field_value)) {
            $field_value = htmlspecialchars($field_value);
        }

        if($field_value === false) {
            $sql = 'UPDATE `'.self::tableName().'` SET '.$field_name.' = false WHERE id = '.$this->id;
        }elseif(empty($field_value)) {
            $sql = 'UPDATE `'.self::tableName().'` SET '.$field_name.' = NULL WHERE id = '.$this->id;
        }else {
            $sql = 'UPDATE `'.self::tableName().'` SET '.$field_name.' = "'.$field_value.'" WHERE id = '.$this->id;
        }

        return Yii::$app->db->createCommand($sql)->execute();
    }


    public function createEvent($operator_sip, $event, $event_time, $ats_eventID) {

        $call_event = new CallEvent();
        $call_event->ats_eventID = $ats_eventID;
        $call_event->call_id = $this->id;
        $call_event->operator_sip = $operator_sip;

        $operator_subscription = OperatorBeelineSubscription::find()->where(['mobile_ats_login' => $operator_sip])->one();
        if($operator_subscription != null) {
            $call_event->operator_user_id = $operator_subscription->user->id;
        }
        $call_event->event = $event;
        $call_event->event_time = $event_time;
        $call_event->created_at = time();
        if(!$call_event->save(false)) {
            return null;
        }

        return $call_event;
    }


    public static function getStatuses() {

        return [
            'successfully_completed' => 'Успешно завершен',
            'quickly_completed' => 'Аномально быстро завершен',
            'not_completed' => 'Разговор не был начат'
        ];
    }


    public static function sentToBrawsersMissedCallsCount() {

//        $missed_calls_count = Call::find()
//            ->where(['call_direction' => 'input'])
//            ->andWhere(['status' => 'not_completed'])
//            ->andWhere(['<', 'ats_eok_time', time()])
//            ->andWhere(['>', 'ats_eok_time', 0])
//            ->count();

        $missed_cases_count = CallCase::find()
            ->where(['case_type' => 'missed'])
            ->andWhere(['status' => 'not_completed'])
            ->count();

        $data = [
            'missed_cases_count' => $missed_cases_count
        ];
        $aUsersIds = []; // всем пользователям уходит сообщение

        SocketDemon::sendOutBrowserMessageInstant('all_site_pages', [''], 'updateMissedCallsCount', $data, $aUsersIds);
    }


    public static function sentToBrawsersIncomingCallsCount() {

        //$incoming_calls_count = Call::find()->where(['finished_at' => NULL])->andWhere(['answered_at' => NULL])->count();

        $incoming_calls_count = Call::find()
            ->where(['t_answer' => NULL])
            ->andWhere(['call_direction' => 'input'])
            ->andWhere([
                'OR',
                ['ats_eok_time' => 0],
                ['ats_eok_time' => NULL],
            ])
            ->count();

        //echo 'incoming_calls_count='.$incoming_calls_count;

        $data = [
            'incoming_calls_count' => $incoming_calls_count
        ];
        $aUsersIds = []; // всем пользователям уходит сообщение

        SocketDemon::sendOutBrowserMessageInstant('all_site_pages', [''], 'updateIncomingCallsCount', $data, $aUsersIds);
    }


    public function getHandlingCallOperator()
    {
        return $this->hasOne(User::className(), ['id' => 'handling_call_operator_id']);
    }

    // создаем/изменяем обращения в момент завершения звонка
    public function createUpdateCase($isCheckFinishedCalls = false) {

        // защита от дублирования записей (такое может быть из-за разницы во времени)
        $docking = CallDocking::find()->where(['call_id' => $this->id])->one();


//        $msg = '';
//        $msg .= 'время: '.date('H:i:s')." time=".time()." - Произошел вызов createUpdateCase() isCheckFinishedCalls=$isCheckFinishedCalls<br />";
//        $msg .= 'call_direction='.$this->call_direction.'<br />';
//        $msg .= 'status='.$this->status.'<br />';
//        //$msg .= 'operand_user='.($operand_user == null ? 'NULL' : 'существует').'<br />';
//
//        $msg .= 't_answer='.$this->t_answer.'<br />';
//        $msg .= 't_hungup='.$this->t_hungup.'<br />';
//        $msg .= 'handling_call_operator_id='.$this->handling_call_operator_id.'<br />';
//        $msg .= ($docking != null ? 'docking существует, выход из функции' : '');
//
//        Yii::$app->mailer->compose()
//            ->setFrom('admin@developer.almobus.ru')
//            ->setTo('test.shetinin@gmail.com')
//            //->setTo('nara-dress@yandex.ru')
//            ->setSubject('сообщение от АТС')
//            //->setTextBody($msg)
//            ->setHtmlBody($msg)
//            ->send();



        if($docking != null) {
            return false;
        }




        // если телефон операнда соответствует номеру одной из "электронных заявок" (заказов в нулевом статусе и с external_type=client_server_request),
        // то проверяем закрывать ли заявку-заказ операнда

        if(!empty($this->t_answer)) {

//            $setting = Setting::find()->where(['id' => 1])->one();
//            if ($setting == null) {
//                throw new ForbiddenHttpException('Не найдена запись с настройками');
//            }

            // если время разговора дольше чем установленная в настройках мин-е время разговора для закрытия заявок
            if($this->t_hungup - $this->t_answer >= Yii::$app->setting->min_talk_time_to_perform_request) {

                $client = Client::find()->where(['mobile_phone' => $this->operand])->one();
                if ($client != null) {
                    $orders = Order::find()
                        ->where(['client_id' => $client->id])
                        ->andWhere(['external_type' => "client_site"])
                        ->andWhere(['status_id' => 0])
                        ->all();
                    // все такие заказы отменяются, но без учета статистики отмененных заказов
                    if (count($orders) > 0) {
                        $order_status = OrderStatus::getByCode('canceled');
                        foreach ($orders as $order) {

                            $order->scenario = 'close_client_server_request';
                            $order->status_id = $order_status->id;
                            $order->status_setting_time = time();
                            $order->comment = 'Отменен автоматически после разговора с клиентом';

                            $order->save(false);
                        }

                        // IncomingOrdersWidget::updateIncomingRequestOrders();
                    }
                }

            }
        }


        //  серым фоном заявку если по ней идет звонок
        //$call->updateIncomingRequestOrders();

        // в любом случае обновляем окно входящих заявок
        IncomingOrdersWidget::updateIncomingRequestOrders();


        // вызываем отсюда пересчет оставшихся минут у СИП-аккаунта (operator_beeline_subscription)
        // потому что createUpdateCase вызывается как правило при завершении звонка, и есть проверка
        // на дублирующий вызов (if($docking != null)

        // звонки бывают завершенные после разговора, бывают завершенные без разговора
        if($this->call_direction == 'output' && !empty($this->t_answer) && !empty($this->t_hungup) && !empty($this->sip)) {
            $operator_subscription = OperatorBeelineSubscription::find()->where(['mobile_ats_login' => $this->sip])->one();
            if($operator_subscription != null) {
                $seconds = $this->t_hungup - $this->t_answer;
                $minutes = ceil($seconds/60); // округляем в большую сторону
                $subscription_minutes = $operator_subscription->minutes - $minutes;
                $operator_subscription->setField('minutes', $subscription_minutes);
            }
        }


        if($this->call_direction == 'input') // входящий звонок
        {
            $operand_user = null;
            if(!empty($this->operand)) {
                $operand_user = User::find()->where(['phone' => $this->operand])->one();
            }


            // если операнду соответствует один из номеров в таблице user
            // это условие скорее всего работать не будет или будет не правильно работать!!!
            if($operand_user != null) {

                if(in_array($this->status, ['not_completed','quickly_completed'])) {

                    $case = new CallCase();
                    $case->case_type = 'administrative_request';
                    //$case->order_id
                    $case->open_time = time();
                    $case->update_time = time();
                    $case->operand = $this->operand;
                    $case->call_count = 1;
                    $case->status = 'adm_completed';
                    $case->close_time = time();
                    if(!$case->save(false)) {
                        return false;
                    }

                    // связываем текущий звонок и обращение
                    $docking = new CallDocking();
                    $docking->call_id = $this->id;
                    $docking->case_id = $case->id;
                    $call_client = Client::find()->where(['mobile_phone' => $this->operand])->one();
                    $docking->conformity = ($call_client != null);
                    if(!$docking->save(false)) {
                        return false;
                    }


                }elseif($this->status == 'successfully_completed') {

                    // если были во время разговора действия с заказом
                    // создание обращений связанных с действиями оператора начиная от начало разговора до момента завершения звонка
                    $dispatcher_accountings = [];
                    if(!empty($this->t_answer) && !empty($this->t_hungup) && !empty($this->handling_call_operator_id)) {

                        $check_actions_time_start = $this->t_answer;
                        $check_actions_time_stop = $this->t_hungup;

                        $dispatcher_accountings = DispatcherAccounting::find()
                            ->where(['dispetcher_id' => $this->handling_call_operator_id])
                            ->andWhere(['>=', 'created_at', $check_actions_time_start])
                            ->andWhere(['<=', 'created_at', $check_actions_time_stop])
                            ->andWhere(['operation_type' => [
                                'order_create',  // 'Первичная запись'
                                'order_update',  // 'Редактирование заказа',
                                'order_confirm', // 'Подтверждение заказа'
                                'order_cancel', // 'Удаление заказа'
                                'order_sat_to_transport', // 'Посадка в машину'
                                'order_unsat_from_transport', // 'Высадка из машины'
                                'order_checked_last_orders' // 'Проверка заказа на дубликаты'
                            ]])
                            ->all();
                    }

                    // если были во время разговора действия с заказом
                    if(count($dispatcher_accountings) > 0) {

                        // ищем уже существующий кейс "действий с заказом" с таким order_id
                        $aOrdersDispatcherAccountings = [];
                        foreach($dispatcher_accountings as $dispatcher_accounting) {
                            $aOrdersDispatcherAccountings[$dispatcher_accounting->order_id][] = $dispatcher_accounting;
                        }

                        foreach($aOrdersDispatcherAccountings as $order_id => $aOrderDispatcherAccountings) {

                            $order_case = CallCase::find()->where(['order_id' => $order_id])->one();
                            if($order_case != null) {

                                $order_case->call_count += 1;
                                $order_case->setField('call_count', $order_case->call_count);
                                $order_case->setField('update_time', time());

                            }else {

                                $order_case = new CallCase();
                                $order_case->case_type = 'operation_with_order';
                                $order_case->order_id = $order_id;
                                $order_case->open_time = time();
                                $order_case->update_time = time();
                                //$order_case->operand = $this->operand;
                                $order_case->call_count = 1;
                                $order_case->status = 'not_completed';
                                if(!$order_case->save(false)) {
                                    return false;
                                }
                            }


                            foreach($aOrderDispatcherAccountings as $dispatcher_accounting) {

                                // связываем текущий звонок и обращение
                                $docking = new CallDocking();
                                $docking->call_id = $this->id;
                                $docking->case_id = $order_case->id;
                                $call_client = Client::find()->where(['mobile_phone' => $this->operand])->one();
                                $docking->conformity = ($call_client != null);
                                $docking->click_event = $dispatcher_accounting->operation_type;
                                if(!$docking->save(false)) {
                                    return false;
                                }
                            }
                        }

                    }else { // если во время разговора не было действий с заказом/заказами

                        $case = new CallCase();
                        $case->case_type = 'administrative_request';
                        //$case->order_id
                        $case->open_time = time();
                        $case->update_time = time();
                        $case->operand = $this->operand;
                        $case->call_count = 1;
                        $case->status = 'adm_completed';
                        $case->close_time = time();
                        if(!$case->save(false)) {
                            return false;
                        }


                        // связываем текущий звонок и обращение
                        $docking = new CallDocking();
                        $docking->call_id = $this->id;
                        $docking->case_id = $case->id;
                        $call_client = Client::find()->where(['mobile_phone' => $this->operand])->one();
                        $docking->conformity = ($call_client != null);
                        if(!$docking->save(false)) {
                            return false;
                        }
                    }
                }


            }else { // если операнду не соответствует номер в таблице user

                if(in_array($this->status, ['not_completed','quickly_completed'])) {

                    $case = CallCase::find()
                        ->where(['case_type' => 'missed'])
                        ->andWhere(['close_time' => NULL])
                        ->andWhere(['status' => 'not_completed'])
                        ->andWhere(['operand' => $this->operand])
                        ->one();

                    if($case != null) {

                        $case->call_count += 1;
                        $case->setField('call_count', $case->call_count);
                        $case->setField('update_time', time());

                    }else {

                        $case = new CallCase();
                        $case->case_type = 'missed';
                        $case->open_time = time();
                        $case->update_time = time();
                        $case->operand = $this->operand;
                        $case->call_count = 1;
                        $case->status = 'not_completed';
                        //$case->close_time = time();
                        if(!$case->save(false)) {
                            return false;
                        }

                        // обновляем в браузерах всех пользователей количество пропущенных звонков
                        Call::sentToBrawsersMissedCallsCount();
                    }

                    // связываем текущий звонок и обращение
                    $docking = new CallDocking();
                    $docking->call_id = $this->id;
                    $docking->case_id = $case->id;
                    $call_client = Client::find()->where(['mobile_phone' => $this->operand])->one();
                    $docking->conformity = ($call_client != null);
                    if(!$docking->save(false)) {
                        return false;
                    }


                }elseif($this->status == 'successfully_completed') {

                    $dispatcher_accountings = [];
                    if(!empty($this->t_answer) && !empty($this->t_hungup) && !empty($this->handling_call_operator_id)) {

                        $check_actions_time_start = $this->t_answer;
                        $check_actions_time_stop = $this->t_hungup;

                        $dispatcher_accountings = DispatcherAccounting::find()
                            ->where(['dispetcher_id' => $this->handling_call_operator_id])
                            ->andWhere(['>=', 'created_at', $check_actions_time_start])
                            ->andWhere(['<=', 'created_at', $check_actions_time_stop])
                            ->andWhere(['operation_type' => [
                                'order_create',  // 'Первичная запись'
                                'order_update',  // 'Редактирование заказа',
                                'order_confirm', // 'Подтверждение заказа'
                                'order_cancel', // 'Удаление заказа'
                                'order_sat_to_transport', // 'Посадка в машину'
                                'order_unsat_from_transport', // 'Высадка из машины'
                                'order_checked_last_orders' // 'Проверка заказа на дубликаты'
                            ]])
                            ->all();
                    }


                    $case = CallCase::find()
                        ->where(['case_type' => 'missed'])
                        ->andWhere(['close_time' => NULL])
                        ->andWhere(['status' => 'not_completed'])
                        ->andWhere(['operand' => $this->operand])
                        ->one();

                    // если есть кейс со статусом 'not_completed', с пустым close_time, и типом 'missed'
                    if($case != null)
                    {
                        // если были во время разговора действия с заказом
                        if(count($dispatcher_accountings) > 0) {

                            // 1-й кейс. Обновляет найденный 'missed'-'not_completed' кейс,
                            // установливает close_time и меняет статус на input_call_missed_completed
                            $case->call_count += 1;
                            $case->update_time = time();
                            $case->close_time = time();
                            $case->status = 'input_call_missed_completed';
                            if(!$case->save(false)) {
                                return false;
                            }

                            // обновляем в браузерах всех пользователей количество пропущенных звонков
                            Call::sentToBrawsersMissedCallsCount();


                            $docking = new CallDocking();
                            $docking->call_id = $this->id;
                            $docking->case_id = $case->id;
                            $call_client = Client::find()->where(['mobile_phone' => $this->operand])->one();
                            $docking->conformity = ($call_client != null);
                            if(!$docking->save(false)) {
                                return false;
                            }



//                           2-й кейс.[
//                                - если есть кейс с типом 'operation_with_order' с таким же order_id и со статусом not_completed
//                                    => Обновляет найденный 'operation_with_order' кейс - а по сути меняется только может привязка с помощью call_docking
//                                - иначе
//                                    => Создает кейс с типом 'operation_with_order' с таким же order_id и со статусом not_completed и с open_time
//                            ]
                            $aOrdersDispatcherAccountings = [];
                            foreach($dispatcher_accountings as $dispatcher_accounting) {
                                $aOrdersDispatcherAccountings[$dispatcher_accounting->order_id][] = $dispatcher_accounting;
                            }

                            foreach($aOrdersDispatcherAccountings as $order_id => $aOrderDispatcherAccountings) {

                                $order_case = CallCase::find()
                                    ->where(['order_id' => $order_id])
                                    //->andWhere(['case_type' => 'operation_with_order'])
                                    //->andWhere(['status' => 'not_completed'])
                                    ->one();
                                if($order_case != null) {

                                    $order_case->call_count += 1;
                                    $order_case->setField('call_count', $order_case->call_count);
                                    $order_case->setField('update_time', time());

                                }else {

                                    $order_case = new CallCase();
                                    $order_case->case_type = 'operation_with_order';
                                    $order_case->order_id = $order_id;
                                    $order_case->open_time = time();
                                    $order_case->update_time = time();
                                    $order_case->call_count = 1;
                                    $order_case->status = 'not_completed';
                                    if(!$order_case->save(false)) {
                                        return false;
                                    }
                                }

                                foreach($aOrderDispatcherAccountings as $dispatcher_accounting) {

                                    // связываем текущий звонок и обращение
                                    $docking = new CallDocking();
                                    $docking->call_id = $this->id;
                                    $docking->case_id = $order_case->id;
                                    $call_client = Client::find()->where(['mobile_phone' => $this->operand])->one();
                                    $docking->conformity = ($call_client != null);
                                    $docking->click_event = $dispatcher_accounting->operation_type;
                                    if(!$docking->save(false)) {
                                        return false;
                                    }
                                }
                            }


                        }else { // не были произведены действия с заказом/заказами

                            // => Обновляет найденный кейс, устанавливает close_time и меняет статус на input_call_missed_completed
                            $case->call_count += 1;
                            $case->close_time = time();
                            $case->update_time = time();
                            $case->status = 'input_call_missed_completed';
                            if(!$case->save(false)) {
                                return false;
                            }

                            // обновляем в браузерах всех пользователей количество пропущенных звонков
                            Call::sentToBrawsersMissedCallsCount();

                            // связываем текущий звонок и обращение
                            $docking = new CallDocking();
                            $docking->call_id = $this->id;
                            $docking->case_id = $case->id;
                            $call_client = Client::find()->where(['mobile_phone' => $this->operand])->one();
                            $docking->conformity = ($call_client != null);
                            if(!$docking->save(false)) {
                                return false;
                            }
                        }

                    }else // если нет кейса со статусом 'not_completed', с пустым close_time, и типом 'missed'
                    {

//                        $msg = '';
//                        $msg .= 'время: '.date('H:i:s')." time=".time()." dispatcher_accountings_count=".count($dispatcher_accountings);
//
//                        Yii::$app->mailer->compose()
//                            ->setFrom('admin@developer.almobus.ru')
//                            ->setTo('test.shetinin@gmail.com')
//                            ->setSubject('сообщение от АТС')
//                            ->setHtmlBody($msg)
//                            ->send();


                        // если были во время разговора действия с заказом
                        if(count($dispatcher_accountings) > 0) {

//                            - если есть кейс с типом 'operation_with_order' и таким же order_id, то
//		                        => Обновляет найденный 'operation_with_order' кейс - а по сути меняется только может привязка с помощью call_docking
//                            - иначе
//		                        => Создает новый кейс, с типом 'operation_with_order', с order_id, с open_time, со статусом not_completed
                            $aOrdersDispatcherAccountings = [];
                            foreach($dispatcher_accountings as $dispatcher_accounting) {
                                $aOrdersDispatcherAccountings[$dispatcher_accounting->order_id][] = $dispatcher_accounting;
                            }


                            foreach($aOrdersDispatcherAccountings as $order_id => $aOrderDispatcherAccountings) {

                                $order_case = CallCase::find()
                                    ->where(['order_id' => $order_id])
                                    //->andWhere(['case_type' => 'operation_with_order'])
                                    //->andWhere(['status' => 'not_completed'])
                                    ->one();


                                if($order_case != null) {

                                    $order_case->call_count += 1;
                                    $order_case->setField('update_time', time());
                                    $order_case->setField('call_count', $order_case->call_count);

                                }else {

                                    $order_case = new CallCase();
                                    $order_case->case_type = 'operation_with_order';
                                    $order_case->order_id = $order_id;
                                    $order_case->open_time = time();
                                    $order_case->update_time = time();
                                    $order_case->call_count = 1;
                                    $order_case->status = 'not_completed';
                                    if(!$order_case->save(false)) {
                                        return false;
                                    }
                                }


                                foreach($aOrderDispatcherAccountings as $dispatcher_accounting) {

                                    // связываем текущий звонок и обращение
                                    $docking = new CallDocking();
                                    $docking->call_id = $this->id;
                                    $docking->case_id = $order_case->id;
                                    $call_client = Client::find()->where(['mobile_phone' => $this->operand])->one();
                                    $docking->conformity = ($call_client != null);
                                    $docking->click_event = $dispatcher_accounting->operation_type;
                                    if(!$docking->save(false)) {
                                        return false;
                                    }
                                }
                            }

                        }else { // не были произведены действия с заказом

                            // => Создается новый кейс, с типом 'information_request',
                            // с open_time, с заполненным close_time, со статусом inf_completed


//                            $msg = '';
//                            $msg .= 'время: '.date('H:i:s')." time=".time()." перед x_1";
//
//                            Yii::$app->mailer->compose()
//                                ->setFrom('admin@developer.almobus.ru')
//                                ->setTo('test.shetinin@gmail.com')
//                                ->setSubject('сообщение от АТС')
//                                ->setHtmlBody($msg)
//                                ->send();

                            $case = new CallCase();
                            $case->case_type = 'information_request';
                            $case->open_time = time();
                            $case->update_time = time();
                            $case->operand = $this->operand;
                            $case->call_count = 1;
                            $case->status = 'inf_completed';
                            $case->close_time = time();
                            if(!$case->save(false)) {
                                return false;
                            }


                            // связываем текущий звонок и обращение
                            $docking = new CallDocking();
                            $docking->call_id = $this->id;
                            $docking->case_id = $case->id;
                            $call_client = Client::find()->where(['mobile_phone' => $this->operand])->one();
                            $docking->conformity = ($call_client != null);
                            if(!$docking->save(false)) {
                                return false;
                            }
                        }
                    }
                }

            }


        }
        elseif($this->call_direction == 'output') { // исходящий звонок

            $operand_user = null;
            if(!empty($this->operand)) {
                $operand_user = User::find()->where(['phone' => $this->operand])->one();
            }

            // если операнду соответствует один из номеров в таблице user
            // это условие скорее всего работать не будет или будет не правильно работать!!!
            if($operand_user != null)
            {
                if(in_array($this->status, ['not_completed','quickly_completed'])) {

                    $case = new CallCase();
                    $case->case_type = 'administrative_request';
                    //$case->order_id
                    $case->open_time = time();
                    $case->update_time = time();
                    $case->operand = $this->operand;
                    $case->call_count = 1;
                    $case->status = 'adm_completed';
                    $case->close_time = time();
                    if(!$case->save(false)) {
                        return false;
                    }

                    // связываем текущий звонок и обращение
                    $docking = new CallDocking();
                    $docking->call_id = $this->id;
                    $docking->case_id = $case->id;
                    $call_client = Client::find()->where(['mobile_phone' => $this->operand])->one();
                    $docking->conformity = ($call_client != null);
                    if(!$docking->save(false)) {
                        return false;
                    }

                }
                elseif($this->status == 'successfully_completed')
                {

                    $dispatcher_accountings = [];
                    if(!empty($this->t_answer) && !empty($this->t_hungup) && !empty($this->handling_call_operator_id)) {

                        $check_actions_time_start = $this->t_answer;
                        $check_actions_time_stop = $this->t_hungup;

                        $dispatcher_accountings = DispatcherAccounting::find()
                            ->where(['dispetcher_id' => $this->handling_call_operator_id])
                            ->andWhere(['>=', 'created_at', $check_actions_time_start])
                            ->andWhere(['<=', 'created_at', $check_actions_time_stop])
                            ->andWhere(['operation_type' => [
                                'order_create',  // 'Первичная запись'
                                'order_update',  // 'Редактирование заказа',
                                'order_confirm', // 'Подтверждение заказа'
                                'order_cancel', // 'Удаление заказа'
                                'order_sat_to_transport', // 'Посадка в машину'
                                'order_unsat_from_transport', // 'Высадка из машины'
                                'order_checked_last_orders' // 'Проверка заказа на дубликаты'
                            ]])
                            ->all();
                    }

                    if(count($dispatcher_accountings) > 0) {

                        // ищем уже существующий кейс "действий с заказом" с таким order_id
                        $aOrdersDispatcherAccountings = [];
                        foreach($dispatcher_accountings as $dispatcher_accounting) {
                            $aOrdersDispatcherAccountings[$dispatcher_accounting->order_id][] = $dispatcher_accounting;
                        }

                        foreach($aOrdersDispatcherAccountings as $order_id => $aOrderDispatcherAccountings) {

                            $order_case = CallCase::find()->where(['order_id' => $order_id])->one();
                            if($order_case != null) {

                                $order_case->call_count += 1;
                                $order_case->setField('update_time', time());
                                $order_case->setField('call_count', $order_case->call_count);

                            }else {

                                $order_case = new CallCase();
                                $order_case->case_type = 'operation_with_order';
                                $order_case->order_id = $order_id;
                                $order_case->open_time = time();
                                $order_case->update_time = time();
                                $order_case->call_count = 1;
                                $order_case->status = 'not_completed';
                                if(!$order_case->save(false)) {
                                    return false;
                                }
                            }


                            foreach($aOrderDispatcherAccountings as $dispatcher_accounting) {

                                // связываем текущий звонок и обращение
                                $docking = new CallDocking();
                                $docking->call_id = $this->id;
                                $docking->case_id = $order_case->id;
                                $call_client = Client::find()->where(['mobile_phone' => $this->operand])->one();
                                $docking->conformity = ($call_client != null);
                                $docking->click_event = $dispatcher_accounting->operation_type;
                                if(!$docking->save(false)) {
                                    return false;
                                }
                            }
                        }

                    }else { // если во время разговора не было действий с заказом/заказами

                        $case = new CallCase();
                        $case->case_type = 'administrative_request';
                        $case->open_time = time();
                        $case->update_time = time();
                        $case->operand = $this->operand;
                        $case->call_count = 1;
                        $case->status = 'adm_completed';
                        $case->close_time = time();
                        if(!$case->save(false)) {
                            return false;
                        }


                        // связываем текущий звонок и обращение
                        $docking = new CallDocking();
                        $docking->call_id = $this->id;
                        $docking->case_id = $case->id;
                        $call_client = Client::find()->where(['mobile_phone' => $this->operand])->one();
                        $docking->conformity = ($call_client != null);
                        if(!$docking->save(false)) {
                            return false;
                        }
                    }
                }

            }
            else // если операнду не соответствует номер в таблице user
            {


                if(in_array($this->status, ['not_completed','quickly_completed'])) {

                    $case = CallCase::find()
                        ->where(['case_type' => 'missed'])
                        ->andWhere(['close_time' => NULL])
                        ->andWhere(['status' => 'not_completed'])
                        ->andWhere(['operand' => $this->operand])
                        ->one();

                    if($case != null) {

                        $case->call_count += 1;
                        $case->setField('update_time', time());
                        $case->setField('call_count', $case->call_count);

                    }else {

                        $case = new CallCase();
                        $case->case_type = 'information_request';
                        $case->open_time = time();
                        $case->update_time = time();
                        $case->operand = $this->operand;
                        $case->call_count = 1;
                        $case->status = 'inf_abnormal_call_completed';
                        $case->close_time = time();
                        if(!$case->save(false)) {
                            return false;
                        }
                    }


                    // связываем текущий звонок и обращение
                    $docking = new CallDocking();
                    $docking->call_id = $this->id;
                    $docking->case_id = $case->id;
                    $call_client = Client::find()->where(['mobile_phone' => $this->operand])->one();
                    $docking->conformity = ($call_client != null);
                    if(!$docking->save(false)) {
                        return false;
                    }

                }
                elseif($this->status == 'successfully_completed')
                {

                    $dispatcher_accountings = [];
                    if(!empty($this->t_answer) && !empty($this->t_hungup) && !empty($this->handling_call_operator_id)) {

                        $check_actions_time_start = $this->t_answer;
                        $check_actions_time_stop = $this->t_hungup;

                        $dispatcher_accountings = DispatcherAccounting::find()
                            ->where(['dispetcher_id' => $this->handling_call_operator_id])
                            ->andWhere(['>=', 'created_at', $check_actions_time_start])
                            ->andWhere(['<=', 'created_at', $check_actions_time_stop])
                            ->andWhere(['operation_type' => [
                                'order_create',  // 'Первичная запись'
                                'order_update',  // 'Редактирование заказа',
                                'order_confirm', // 'Подтверждение заказа'
                                'order_cancel', // 'Удаление заказа'
                                'order_sat_to_transport', // 'Посадка в машину'
                                'order_unsat_from_transport', // 'Высадка из машины'
                                'order_checked_last_orders' // 'Проверка заказа на дубликаты'
                            ]])
                            ->all();
                    }


                    $case = CallCase::find()
                        ->where(['case_type' => 'missed'])
                        ->andWhere(['close_time' => NULL])
                        ->andWhere(['status' => 'not_completed'])
                        ->andWhere(['operand' => $this->operand])
                        ->one();

                    if($case != null) {
                        $docking = new CallDocking();
                        $docking->call_id = $this->id;
                        $docking->case_id = $case->id;
                        $call_client = Client::find()->where(['mobile_phone' => $this->operand])->one();
                        $docking->conformity = ($call_client != null);
                        if (!$docking->save(false)) {
                            return false;
                        }
                    }

                    // если есть кейс со статусом 'not_completed', с пустым close_time, и типом 'missed'
                    if($case != null)
                    {
                        // если были во время разговора действия с заказом
                        if(count($dispatcher_accountings) > 0)
                        {

                            // 1-й кейс. Обновляет найденный 'missed'-'not_completed' кейс,
                            // установливает close_time и меняет статус на input_call_missed_completed
                            $case->call_count += 1;
                            $case->update_time = time();
                            $case->close_time = time();

                            if($this->caused_by_missed_call_window == true) {
                                $case->status = 'missed_completed';
                            }else {
                                $case->status = 'output_call_missed_completed';
                            }
                            if(!$case->save(false)) {
                                return false;
                            }

                            // обновляем в браузерах всех пользователей количество пропущенных звонков
                            Call::sentToBrawsersMissedCallsCount();

//                           2-й кейс.[
//                                - если есть кейс с типом 'operation_with_order' с таким же order_id и со статусом not_completed
//                                    => Обновляет найденный 'operation_with_order' кейс - а по сути меняется только может привязка с помощью call_docking
//                                - иначе
//                                    => Создает кейс с типом 'operation_with_order' с таким же order_id и со статусом not_completed и с open_time
//                            ]
                            $aOrdersDispatcherAccountings = [];
                            foreach($dispatcher_accountings as $dispatcher_accounting) {
                                $aOrdersDispatcherAccountings[$dispatcher_accounting->order_id][] = $dispatcher_accounting;
                            }

                            foreach($aOrdersDispatcherAccountings as $order_id => $aOrderDispatcherAccountings) {

                                $order_case = CallCase::find()
                                    ->where(['order_id' => $order_id])
                                    //->andWhere(['case_type' => 'operation_with_order'])
                                    //->andWhere(['status' => 'not_completed'])
                                    ->one();
                                if($order_case != null) {

                                    $order_case->call_count += 1;
                                    $order_case->setField('update_time', time());
                                    $order_case->setField('call_count', $order_case->call_count);

                                }else {

                                    $order_case = new CallCase();
                                    $order_case->case_type = 'operation_with_order';
                                    $order_case->order_id = $order_id;
                                    $order_case->open_time = time();
                                    $order_case->update_time = time();
                                    $order_case->call_count = 1;
                                    $order_case->status = 'not_completed';
                                    if(!$order_case->save(false)) {
                                        return false;
                                    }
                                }


                                foreach($aOrderDispatcherAccountings as $dispatcher_accounting) {

                                    // связываем текущий звонок и обращение
                                    $docking = new CallDocking();
                                    $docking->call_id = $this->id;
                                    $docking->case_id = $order_case->id;
                                    $call_client = Client::find()->where(['mobile_phone' => $this->operand])->one();
                                    $docking->conformity = ($call_client != null);
                                    $docking->click_event = $dispatcher_accounting->operation_type;
                                    if(!$docking->save(false)) {
                                        return false;
                                    }
                                }
                            }


                        }
                        else // не были произведены действия с заказом/заказами
                        {

                            // => Обновляет найденный кейс, устанавливает close_time и меняет статус на input_call_missed_completed
                            $case->call_count += 1;
                            $case->update_time = time();
                            $case->close_time = time();

                            // 'not_completed','adm_completed','inf_completed','missed_completed','input_call_missed_completed','output_call_missed_completed','auto_completed','inf_abnormal_call_completed'
                            if($this->caused_by_missed_call_window == true) {
                                $case->status = 'missed_completed';
                            }else {
                                $case->status = 'output_call_missed_completed';
                            }

                            if(!$case->save(false)) {
                                return false;
                            }

                            // обновляем в браузерах всех пользователей количество пропущенных звонков
                            Call::sentToBrawsersMissedCallsCount();

                            // связываем текущий звонок и обращение
                            $docking = new CallDocking();
                            $docking->call_id = $this->id;
                            $docking->case_id = $case->id;
                            $call_client = Client::find()->where(['mobile_phone' => $this->operand])->one();
                            $docking->conformity = ($call_client != null);
                            if(!$docking->save(false)) {
                                return false;
                            }
                        }

                    }else // если нет кейса со статусом 'not_completed', с пустым close_time, и типом 'missed'
                    {
                        // если были во время разговора действия с заказом
                        if(count($dispatcher_accountings) > 0) {

//                            - если есть кейс с типом 'operation_with_order' и таким же order_id, то
//		                        => Обновляет найденный 'operation_with_order' кейс - а по сути меняется только может привязка с помощью call_docking
//                            - иначе
//		                        => Создает новый кейс, с типом 'operation_with_order', с order_id, с open_time, со статусом not_completed
                            $aOrdersDispatcherAccountings = [];
                            foreach($dispatcher_accountings as $dispatcher_accounting) {
                                $aOrdersDispatcherAccountings[$dispatcher_accounting->order_id][] = $dispatcher_accounting;
                            }


                            foreach($aOrdersDispatcherAccountings as $order_id => $aOrderDispatcherAccountings) {

                                $order_case = CallCase::find()
                                    ->where(['order_id' => $order_id])
                                    //->andWhere(['case_type' => 'operation_with_order'])
                                    //->andWhere(['status' => 'not_completed'])
                                    ->one();
                                if($order_case != null) {

                                    $order_case->call_count += 1;
                                    $order_case->setField('update_time', time());
                                    $order_case->setField('call_count', $order_case->call_count);

                                }else {

                                    $order_case = new CallCase();
                                    $order_case->case_type = 'operation_with_order';
                                    $order_case->order_id = $order_id;
                                    $order_case->open_time = time();
                                    $order_case->update_time = time();
                                    $order_case->call_count = 1;
                                    $order_case->status = 'not_completed';
                                    if(!$order_case->save(false)) {
                                        return false;
                                    }
                                }


                                foreach($aOrderDispatcherAccountings as $dispatcher_accounting) {

                                    // связываем текущий звонок и обращение
                                    $docking = new CallDocking();
                                    $docking->call_id = $this->id;
                                    $docking->case_id = $order_case->id;
                                    $call_client = Client::find()->where(['mobile_phone' => $this->operand])->one();
                                    $docking->conformity = ($call_client != null);
                                    $docking->click_event = $dispatcher_accounting->operation_type;
                                    if(!$docking->save(false)) {
                                        return false;
                                    }
                                }
                            }

                        }else { // не были произведены действия с заказом и нет кейса с типом 'missed'

                            // => Создается новый кейс, с типом 'information_request',
                            // с open_time, с заполненным close_time, со статусом inf_completed

                            $case = new CallCase();
                            $case->case_type = 'information_request';
                            $case->open_time = time();
                            $case->update_time = time();
                            $case->operand = $this->operand;
                            $case->call_count = 1;
                            $case->status = 'inf_completed';
                            $case->close_time = time();
                            if(!$case->save(false)) {
                                return false;
                            }


                            // связываем текущий звонок и обращение
                            $docking = new CallDocking();
                            $docking->call_id = $this->id;
                            $docking->case_id = $case->id;
                            $call_client = Client::find()->where(['mobile_phone' => $this->operand])->one();
                            $docking->conformity = ($call_client != null);
                            if(!$docking->save(false)) {
                                return false;
                            }
                        }
                    }
                }
            }
        }
    }

    // отмечаем серым фоном заявку если по ней идет звонок
    public function updateIncomingRequestOrders() {

        if(!empty($this->operand)) {

            $client = Client::find()->where(['mobile_phone' => $this->operand])->one();
            if($client != null) {
                $order = Order::find()
                    ->where(['client_id' => $client->id])
                    ->andWhere(['external_type' => 'client_site'])
                    ->andWhere(['status_id' => 0])
                    ->one();
                if($order != null) {
                    IncomingOrdersWidget::updateIncomingRequestOrders();
                }
            }
        }
    }

    // отправка на сервер по сокетам окна звонка (окна с данными клиента который висит на линии)
    public function sendToBrawserCallWindow() {

        if(empty($this->handling_call_operator_id)) {
            return false;
        }

        $aUsersIds = [
            $this->handling_call_operator_id
        ];
        $data = [
            'call_id' => $this->id,
            'new_page_url' => '/call/get-call-window?call_id='.$this->id,
            //'html' => $this->getGetCallWindow()
            'html' => $this->getCallWindowThroughController($this->handling_call_operator_id)
        ];

        //SocketDemon::sendOutBrowserMessageInstant('all_pages', [''], 'openCallWindow', $data, $aUsersIds);
        SocketDemon::sendOutBrowserMessageInstant('new_page', [''], 'openCallWindow', $data, $aUsersIds);

        return true;
    }

    public function getCallWindowThroughController($user_id) {

        $url = 'http://'.$_SERVER['HTTP_HOST'].'/call/get-call-window?call_id='.$this->id.'&user_id='.$user_id.'&without_json=1';
        return file_get_contents($url);
    }

    public function getGetCallWindow($user_id = 0) {

        // ! Нельзя вызвать именно вид(html,css,js) из консоли - потеря сессии и другие несостыковки в Yii вылазят

        if($user_id == 0) {
            $user = Yii::$app->user->identity;
            if($user == null) {
                throw new ForbiddenHttpException('Через сессию не найден текущий пользователь и не передан в запрос id пользователя');
            }
        }else {
            $user = User::find()->where(['id' => $user_id])->one();
            if($user == null) {
                throw new ForbiddenHttpException('Пользователь с user_id='.$user_id.' не найден');
            }
            Yii::$app->user->identity = $user;
        }


        //$start = microtime(true);
        $client = (!empty($this->operand) ? Client::getClientByMobilePhone($this->operand) : '');
        $orderSearchModel = new OrderSearch();
        $client_id = ($client != null ? $client->id : 0);

        $orderDataProvider = $orderSearchModel->getLastOrdersSearch($client_id);

        if($this->t_answer > 0) {
            $call_speaking_seconds = time() - $this->t_answer + 2; //2 секунды - это примерно время от формирования окна на сервере до того как окно со всем js откроется в браузере
            $start_speaking = true;
        }else {
            $call_speaking_seconds = 0;
            $start_speaking = false;
        }

        $searchOrderByPhoneDataProvider = $orderSearchModel->getSearchOrdersByPhone($this->operand);

        //return \Yii::$app->view->renderAjax('@app/views/call/call-window.php', [
        return \Yii::$app->view->render('@app/views/call/call-window.php', [

            'client_id' => $client_id,

            'call_id' => $this->id,
            'client_phone' => $this->operand,
            'client' => $client,
            'call_speaking_seconds' => $call_speaking_seconds,
            'start_speaking' => $start_speaking,

            'orderSearchModel' => $orderSearchModel,
            'orderDataProvider' => $orderDataProvider,
            'searchOrderByPhoneDataProvider' => $searchOrderByPhoneDataProvider,
        ]);

    }

    public static function getRecordList($beeline_token_api) {

        //$headers[] = 'X-MPBX-API-AUTH-TOKEN: cda7719f-1dcc-46f6-937a-23f1d46dcf75';
        $headers[] = 'X-MPBX-API-AUTH-TOKEN: '.$beeline_token_api;
        $headers[] = 'Content-Type: application/json; charset=UTF-8';

        $ch = curl_init();

        //$record_id = '206636195';
        //$record_id = '185595242';

        // curl -X GET --header 'X-MPBX-API-AUTH-TOKEN: 442283ec-c78f-4252-beca-7d5a11168f94'
        // 'https://cloudpbx.beeline.ru/apis/portal/records'
        $url = 'https://cloudpbx.beeline.ru/apis/portal/records';
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);

        //echo "<pre>"; print_r($result); echo "</pre>";
        $aResult = json_decode($result);

        if(count($aResult) == 0) {
            throw new ForbiddenHttpException('Не удалось прочитать список записей у биллайна, попробуйте еще раз');
        }

        $aRecordsId = [];
        foreach($aResult as $record) {
            $aRecordsId[] = $record->id;
        }

        return $aRecordsId;
    }

    public static function getDownloadRecord($beeline_token_api, $record_id) {

        $headers[] = 'X-MPBX-API-AUTH-TOKEN: '.$beeline_token_api;
        $headers[] = 'Content-Type: application/json; charset=UTF-8';

        $ch = curl_init();

        // curl -X GET --header 'X-MPBX-API-AUTH-TOKEN: 442283ec-c78f-4252-beca-7d5a11168f94'
        // 'https://cloudpbx.beeline.ru/apis/portal/v2/records/206636195/download'
        $url = 'https://cloudpbx.beeline.ru/apis/portal/v2/records/'.$record_id.'/download';
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $record_file_content = curl_exec($ch);
        curl_close($ch);

        return $record_file_content;
    }

    public static function moveFilesToServer($ftp_server, $ftp_user_name, $ftp_user_pass, $ftp_dir_path, $current_server_files_dir_path, $aFilesPathNames) {

        // перенос файлов по ftp на другой сервер
        $conn_id = ftp_connect($ftp_server) or die("Не удалось установить соединение с $ftp_server");
        if($conn_id == false) {
            throw new ForbiddenHttpException('Не удалось установить ftp-соединение');
        }

        // вход с именем пользователя и паролем
        if(!ftp_login($conn_id, $ftp_user_name, $ftp_user_pass)) {
            throw new ForbiddenHttpException('Не удалось залогиниться на ftp-сервере');
        }

        // upload a file
        foreach($aFilesPathNames as $file_name) {

            $file = $current_server_files_dir_path . $file_name;
            $remote_file = $ftp_dir_path.'/' . $file_name;
            if (!ftp_put($conn_id, $remote_file, $file, FTP_ASCII)) {
                throw new ForbiddenHttpException('Проблемы загрузки по ftp файла ' . $file_name);
            }
        }

        ftp_close($conn_id);

        return true;
    }
}
