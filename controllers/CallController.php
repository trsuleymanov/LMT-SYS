<?php

namespace app\controllers;

use app\models\CallCase;
use app\models\CallEvent;
use app\models\Client;
use app\models\Driver;
use app\models\OperatorBeelineSubscription;
use app\models\OrderSearch;
use app\models\SocketDemon;
use app\models\User;
use Codeception\Module\Cli;
use Yii;
use app\models\Call;
use app\models\CallSearch;
use yii\base\ErrorException;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;


class CallController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }


    public function actionAjaxMakeCall() {

        Yii::$app->response->format = 'json';

        $post = Yii::$app->request->post();
        if(!isset($post['phone'])) {
            throw new ForbiddenHttpException('Нет номера телефона получателя');
        }

        if(isset($post['caused_by_missed_call_window']) && $post['caused_by_missed_call_window'] == "true") {
            $caused_by_missed_call_window = true;
        }else {
            $caused_by_missed_call_window = false;
        }

        $user = Yii::$app->user->identity;
        if($user == null) {
            throw new ErrorException('Текущий пользователь не определен');
        }
        $operator_subscription = OperatorBeelineSubscription::find()->where(['operator_id' => $user->id])->one();
        if($operator_subscription == null) {
            throw new ForbiddenHttpException('Оператор не подписан');
        }

        if($operator_subscription->status == 'BREAK') {
            throw new ForbiddenHttpException('Вы на перерыве, поэтому позвонить не можете');
        }
        if($operator_subscription->status == 'OFFLINE') {
            throw new ForbiddenHttpException('Вы отключены, поэтому позвонить не можете');
        }


        $phone = $post['phone'];
        if($phone[0] == '7') {
            $phone = substr($phone, 1);
        }


        // текущие активные звонки
        $active_call = Call::find()
            ->where(['operand' => Client::convertMobilePhone($phone)])
            ->andWhere([
                'OR',
                ['>', 't_hungup', time()],
                ['ats_eok_time' => 0],
                ['ats_eok_time' => NULL],
            ])->one();
        if($active_call != null) {
            throw new ForbiddenHttpException('Линия занята');
        }


        //$headers[] = 'X-MPBX-API-AUTH-TOKEN: b3469183-f19a-46ce-9b44-19ace72e84c2';
        $headers[] = 'X-MPBX-API-AUTH-TOKEN: '.Yii::$app->params['subscription_id'];
        $headers[] = 'Content-Type: application/json; charset=UTF-8';

        $ch = curl_init();

        $url = 'https://cloudpbx.beeline.ru/apis/portal/abonents/'.$operator_subscription->mobile_ats_login.'/call?phoneNumber='.$phone;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);


        //$aResult = json_decode($result, true); // приходит пустой ответ
        if(isset($aResult['errorCode'])) {
            return [
                'success' => false,
                'error' => $aResult['description']
            ];
        }else {

            $call = new Call();
            $call->t_create = time();
            $call->ext_tracking_id = $result;
            $call->operand = Client::convertMobilePhone($phone);
            $call->call_direction = 'output';
            $call->handling_call_operator_id = $operator_subscription->operator_id;
            $call->status = 'not_completed';
            if($caused_by_missed_call_window == true) {
                $call->caused_by_missed_call_window = true;
            }
            if (!$call->save(false)) {
               throw new ForbiddenHttpException('Не удалось создать звонок');
            }

            // отправляем в браузер оператору окно звонка с данными клиента
            $is_sended_to_brawser_call_window = -1;
            if(!$call->sendToBrawserCallWindow()) {
                //throw new ForbiddenHttpException('Не удалось отправить окно звонка handling_call_operator_id='.$call->handling_call_operator_id );
                $is_sended_to_brawser_call_window = false;
            }else {
                $is_sended_to_brawser_call_window = true;
            }

            $aFields = [
                'call_id' => $call->id,
                'ats_callId' => $call->ext_tracking_id,
                'ats_subscriptionId' => $operator_subscription->subscription_id,
                'event_name' => 'Создание звонка из CRM',
                'ats_eventID' => '',
                'crm_event_time' => $call->t_create,
                'ats_event_time' => '',
                'call_type' => $call->call_direction,
                'sip' => $operator_subscription->mobile_ats_login,
                'client_phone' => $call->operand,
            ];
            CallEvent::saveLog($aFields);

            // отмечаем серым фоном заявку если по ней идет звонок
            $call->updateIncomingRequestOrders();


            return [
                'success' => true,
                'result' => $result,
                'is_sended_to_brawser_call_window' => $is_sended_to_brawser_call_window
            ];
        }
    }


    public function actionAjaxGetMissedCallList() {

        Yii::$app->response->format = 'json';

        // ищем не пропущенные звонки, а пропущенные обращения:
//        $missed_calls = Call::find()
//            ->where(['call_from_operator' => 0])
//            ->andWhere(['status' => 'not_completed'])
//            ->andWhere(['<', 'ats_eok_time', time()])
//            ->andWhere(['>', 'ats_eok_time', 0])
//            ->all();

        $missed_cases = CallCase::find()
            ->where(['case_type' => 'missed'])
            ->andWhere(['status' => 'not_completed'])
            ->all();

        // текущие активные звонки
        $active_calls = Call::find()
            ->where([
                'OR',
                ['>', 't_hungup', time()],
                ['ats_eok_time' => 0],
                ['ats_eok_time' => NULL],
            ])->all();

        $clients = Client::find()->where(['mobile_phone' => ArrayHelper::map($missed_cases, 'operand', 'operand')])->all();
        $aClientsPhone = ArrayHelper::index($clients, 'mobile_phone');


        return [
            'success' => true,
            'html' => $this->renderPartial('missed-call-list.php', [
                'missed_cases' => $missed_cases,
                'active_calls' => $active_calls,
                'aClientsPhone' => $aClientsPhone
            ])
        ];
    }


    public function actionGetCallWindow($call_id = 0, $user_id = 0, $operand_phone = '', $without_json = 0) {

        if($without_json == 0) {
            Yii::$app->response->format = 'json';
        }

        //$start = microtime(true);
        if($call_id > 0) {
            $call = Call::find()->where(['id' => $call_id])->one();
            if ($call == null) {
                throw new ForbiddenHttpException('Звонок не найден');
            }

            $operand_phone = $call->operand;

        }else {

            $operand_phone = trim($operand_phone);
            if(empty($operand_phone)) {
                throw new ForbiddenHttpException('Не передан телефон');
            }

            //$operand_phone = "+7-111-111-1111";
            if(substr($operand_phone, 0, 1) != '+') {
                $operand_phone = '+'.$operand_phone;
            }
            //$call = null;

            $call = Call::find()
                ->where(['operand' => $operand_phone])
                ->AndWhere([
                    'OR',
                    ['>', 't_hungup', time()],
                    ['ats_eok_time' => 0],
                    ['ats_eok_time' => NULL],
                ])
                ->one();
        }


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
        $client = Client::getClientByMobilePhone($operand_phone);
        $orderSearchModel = new OrderSearch();
        $client_id = ($client != null ? $client->id : 0);

        $orderDataProvider = $orderSearchModel->getLastOrdersSearch($client_id);

        if($call != null && $call->t_answer > 0) {
            $call_speaking_seconds = time() - $call->t_answer + 2; //2 секунды - это примерно время от формирования окна на сервере до того как окно со всем js откроется в браузере
            $start_speaking = true;
        }else {
            $call_speaking_seconds = 0;
            $start_speaking = false;
        }

        $searchOrderByPhoneDataProvider = $orderSearchModel->getSearchOrdersByPhone($operand_phone);


        if($without_json == 0) {

            return [
                'success' => true,
                //'operand_phone' => $operand_phone,
                'html' => $this->renderAjax('call-window-content', [
                    'client_id' => $client_id,
                    'call_id' => ($call != null ? $call->id : ''),
                    'client_phone' => $operand_phone,
                    'client' => $client,
                    'call_speaking_seconds' => $call_speaking_seconds,
                    'start_speaking' => $start_speaking,

                    'orderSearchModel' => $orderSearchModel,
                    'orderDataProvider' => $orderDataProvider,
                    'searchOrderByPhoneDataProvider' => $searchOrderByPhoneDataProvider,
                ])

            ];

        }else {

            return $this->renderAjax('call-window', [
                'client_id' => $client_id,
                'call_id' => ($call != null ? $call->id : ''),
                'client_phone' => $operand_phone,
                'client' => $client,
                'call_speaking_seconds' => $call_speaking_seconds,
                'start_speaking' => $start_speaking,

                'orderSearchModel' => $orderSearchModel,
                'orderDataProvider' => $orderDataProvider,
                'searchOrderByPhoneDataProvider' => $searchOrderByPhoneDataProvider,
            ]);
        }

    }


/*
    public function actionAjaxGetCallWindow($call_id = 0, $user_id = 0, $operand_phone = '') {

        Yii::$app->response->format = 'json';

        if($call_id > 0) {
            $call = Call::find()->where(['id' => $call_id])->one();
            if ($call == null) {
                throw new ForbiddenHttpException('Звонок не найден');
            }
        }else {

            $operand_phone = trim($operand_phone);
            if(empty($operand_phone)) {
                throw new ForbiddenHttpException('Не передан телефон');
            }

            //$operand_phone = "+7-111-111-1111";
            if(substr($operand_phone, 0, 1) != '+') {
                $operand_phone = '+'.$operand_phone;
            }

            $call = Call::find()
                ->where(['operand' => $operand_phone])
                ->andWhere([
                    'OR',
                    ['>', 't_hungup', time()],
                    ['ats_eok_time' => 0],
                    ['ats_eok_time' => NULL],
                ])
                ->one();
            if($call == null) {
                throw new ForbiddenHttpException('Активный звонок привязанный к номеру телефона '.$operand_phone.' не найден');
            }
        }

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
        $client = (!empty($call->operand) ? Client::getClientByMobilePhone($call->operand) : '');
        $orderSearchModel = new OrderSearch();
        $client_id = ($client != null ? $client->id : 0);

        $orderDataProvider = $orderSearchModel->getLastOrdersSearch($client_id);

        if($call->t_answer > 0) {
            $call_speaking_seconds = time() - $call->t_answer + 2; //2 секунды - это примерно время от формирования окна на сервере до того как окно со всем js откроется в браузере
            $start_speaking = true;
        }else {
            $call_speaking_seconds = 0;
            $start_speaking = false;
        }

        $searchOrderByPhoneDataProvider = $orderSearchModel->getSearchOrdersByPhone($call->operand);

        //return \Yii::$app->view->renderAjax('@app/views/call/call-window.php', [
        return [
            'success' => true,
            'html' => $this->renderAjax('call-window.php', [

                'client_id' => $client_id,

                'call_id' => $call->id,
                'client_phone' => $call->operand,
                'client' => $client,
                'call_speaking_seconds' => $call_speaking_seconds,
                'start_speaking' => $start_speaking,

                'orderSearchModel' => $orderSearchModel,
                'orderDataProvider' => $orderDataProvider,
                'searchOrderByPhoneDataProvider' => $searchOrderByPhoneDataProvider,
            ])
        ];
    }
*/

    // устарел - теперь нужно обновлять не отдельный список заказов, а целиком окно звонков (т.е. вызывать actionGetCallWindow)
//    public function actionAjaxGetClientLastOrders($client_phone) {
//
//        Yii::$app->response->format = 'json';
//
//        //$get_call_data = Yii::$app->getRequest()->post('get_call_data');
//
//        $client = Client::getClientByMobilePhone($client_phone);
//        $orderSearchModel = new OrderSearch();
//        $client_id = ($client != null ? $client->id : 0);
//        $orderDataProvider = $orderSearchModel->getLastOrdersSearch($client_id);
//
//        $data = [
//            'success' => true,
//            //'html' => $this->renderAjax('@app/views/call/client_data_form', [ // передать здесь js-код не удается в браузер...
//            'client_data_html' => $this->renderPartial('@app/views/call/client_data_form', [
//                'phone' => $client_phone,
//                'client' => $client,
//                'orderSearchModel' => $orderSearchModel,
//                'orderDataProvider' => $orderDataProvider,
//            ])
//        ];
//
//
//        return $data;
//    }


    public function actionAjaxSetOperatorStatus($status) {

        Yii::$app->response->format = 'json';

        if(!in_array($status, ['ONLINE', 'OFFLINE'])) {
            throw new ForbiddenHttpException('Нельзя установить статус '.$status);
        }

        $user = Yii::$app->user->identity;
        if($user == null) {
            throw new ErrorException('Текущий пользователь не определен');
        }

        $operator_subscription = OperatorBeelineSubscription::find()->where(['operator_id' => $user->id])->one();
        if($operator_subscription == null) {
            throw new ForbiddenHttpException('Оператор не подписан');
        }

        if($status == 'ONLINE') { // если устанавливается статус ONLINE

            // проверяется что текущая подписка существует в АТС
            // если текущая подписка не существует в АТС, то она пересоздается в АТС и CRM
            // устанавливается статус ONLINE в АТС

            if(!$operator_subscription->isExistInAts()) {

                $operator_subscription->operator_id = NULL;
                $operator_subscription->subscription_id = NULL;
                $operator_subscription->expire_at = NULL;
                if(!$operator_subscription->save(false)) {
                    throw new ErrorException('Не удалось удалить остатки подписки из CRM');
                }

                throw new ErrorException('Подписка слетела! Подключитесь к СИП снова');
//                if(!$operator_subscription->createAtsSubscription()) {
//                    throw new ErrorException('Не удалось пересоздать подписку');
//                }
            }

            $res = $operator_subscription->setStatus($status);
            if($res !== true) {
                return [
                    'success' => false,
                    //'error' => 'Не удалось установить статус '.$status
                    'error' => $res
                ];
            }


        }elseif($status == 'OFFLINE') {

            // устанавливается статус OFFLINE в АТС
            if(!$operator_subscription->setStatus($status)) {
                return [
                    'success' => false,
                    'error' => 'Не удалось установить статус '.$status
                ];
            }
        }

        return [
            'success' => true,
        ];
    }

    public function actionAjaxDeleteIncomingCalls() {

        Yii::$app->response->format = 'json';

        $incoming_calls = Call::find()
            ->where(['t_answer' => NULL])
            ->andWhere(['call_direction' => 'input'])
            ->andWhere([
                'OR',
                ['ats_eok_time' => 0],
                ['ats_eok_time' => NULL],
            ])->all();
        if(count($incoming_calls) == 0) {
            throw new ForbiddenHttpException('Входящие звонки не найдены');
        }

        throw new ForbiddenHttpException('Удаление так и не сделано!');

        return [
            'success' => true,
        ];
    }
}
