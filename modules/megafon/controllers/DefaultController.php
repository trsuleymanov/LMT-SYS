<?php
namespace app\modules\megafon\controllers;

//use yii\filters\auth\HttpBearerAuth;
use app\models\Call;
use app\models\Client;
use app\models\Driver;
use app\models\Order;
use app\models\SocketDemon;
use Yii;
use yii\base\ErrorException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;

/**
 * Единая входная точка для запросов от АТС мегафона
 */
//class DefaultController extends \yii\rest\ActiveController
class DefaultController extends Controller
{
    public $modelClass = '';
    //public $modelClass = 'app\models\User';

//    public function behaviors()
//    {
//        $behaviors = parent::behaviors();
//        $behaviors['authenticator'] = [
//            'class' => HttpBearerAuth::className(),
//        ];
//        $behaviors['authenticator']['except'] = [
//            'driverlogin'
//        ];
//
//        return $behaviors;
//    }

    /**
     * @throws BadRequestHttpException
     */
    public function actionIndex()
    {
        Yii::$app->response->format = 'json';

//        $msg = '';
//        foreach(Yii::$app->request->post() as $key => $val) {
//            $msg .= $key.'='.$val."<br />";
//        }


//        Yii::$app->mailer->compose()
//            ->setFrom('admin@developer.almobus.ru')
//            ->setTo('vlad.shetinin@gmail.com')
//            //->setTo('nara-dress@yandex.ru')
//            ->setSubject('сообщение от АТС')
//            //->setTextBody($msg)
//            ->setHtmlBody($msg)
//            ->send();

        // отсюда шлем сигнал через сокет-демона к клиенту в браузер
        $post = Yii::$app->request->post();
        if($post['cmd'] == 'event') {


            if(in_array($post['type'], ['OUTGOING', 'ACCEPTED', 'COMPLETED', 'CANCELLED'])) {

                $call = Call::find()->where(['callid' => $post['callid']])->one();
                if ($call == null) {

                    $msg = '';
                    $msg .= 'время=' . date('d.m.Y H:i:s', time()) . '<br />';
                    foreach (Yii::$app->request->post() as $key => $val) {
                        $msg .= $key . '=' . $val . "<br />";
                    }
                    Call::sendErrorEmailToAdmin('Не найдена запись о звонке', $msg);

                    throw new ErrorException('Не найдена запись о звонке');
                }
            }


            switch ($post['type']) {
                case 'OUTGOING':
                    // оператор совершает исходящий звонок (в это время АТС пытается дозвониться до клиента)
                    // т.е. оператор взял трубку при звонке из АТС и происходит звонок клиенту

                    //$call = new Call();
                    //$call->callid = $post['callid'];
                    //$call->is_opening = true;
                    //$post['user'] = 'vlad@trsuleymanov.megapbx.ru';
                    //$call->user_id =
                    //$call->mobile_ats_login =
                    //$post['phone'] = 79661128006
                    //$call->call_from_operator = true;

                    $call->outgoing_time = time();
                    if(!$call->save(false)) {
                        throw new ErrorException('Не удалось сохранить время OUTGOING');
                    }

                    $aUsersIds = [$call->user_id];
                    $data = [
                        'is_outgoing_call' => true,
                        'call_id' => $call->id,
                    ];
                    SocketDemon::sendOutBrowserMessageInstant('all_site_pages', [''], 'updateIncomingCall', $data, $aUsersIds);

                    break;

                case 'INCOMING':

                    // Позже доработать!!!

                    // А вот здесь вероятно нужно создать новый звонок в базе данных:

                    $call = new Call();
                    $call->is_opening = true;
                    $call->callid = trim($post['callid']);
                    $call->user_phone = trim($post['telnum']);

                    // здесь не заполняю user_id, потому что к одному номеру может быть привязано множество операторов

//                    $call->user_id = Yii::$app->user->id;
//                    $user = User::find()->where(['id' => Yii::$app->user->id])->one();
//                    if($user == null) {
//                        throw new ForbiddenHttpException('Оператор не найден');
//                    }
//                    $call->mobile_ats_login = $user->mobile_ats_login;

                    $call->subscriber_phone = trim($post['phone']);

                    // +7-966-112-8006
                    $search_phone = Call::convertClearPhoneToTobusPhone($call->subscriber_phone);

                    $client = Client::find()->where([
                        'OR',
                        ['mobile_phone' => $search_phone],
                        ['home_phone' => $search_phone],
                        ['alt_phone' => $search_phone],
                    ])->one();
                    if($client == null) {
                        // нет в данных клиентов, поищем в заказах...
                        $order = Order::find()->where([
                                'OR',
                                ['additional_phone_1' => $search_phone],
                                ['additional_phone_2' => $search_phone],
                                ['additional_phone_3' => $search_phone],
                            ])->one();
                        if($order != null) {
                            $call->subscriber_type = 'client';
                            $call->subscriber_id = $client->id;
                        }
                    }else {
                        $call->subscriber_type = 'client';
                        $call->subscriber_id = $client->id;
                    }

                    if(empty($call->subscriber_type)) {
                        $driver = Driver::find()->where([
                            'OR',
                            ['mobile_phone' => $search_phone],
                            ['home_phone' => $search_phone],
                        ])->one();
                        if($driver != null) {
                            $call->subscriber_type = 'driver';
                            $call->subscriber_id = $driver->id;
                        }
                    }

                    $call->call_from_operator = false;

                    $call->incoming_time = time();
                    if(!$call->save(false)) {
                        throw new ErrorException('Не удалось сохранить время INCOMING');
                    }

                    $aUsersIds = [$call->user_id];
                    $data = [
                        'is_incoming_call' => true,
                        'call_id' => $call->id,
                    ];
                    SocketDemon::sendOutBrowserMessageInstant('all_site_pages', [''], 'updateIncomingCall', $data, $aUsersIds);

                    break;

                case 'ACCEPTED':

                    // при звонке от оператора к клиенту этого параметра нет
                    if(empty($call->user_phone) && isset($post['telnum'])) {
                        $call->user_phone = trim($post['telnum']);
                    }

//                    if(empty($call->user_id)) {
//                        // если это был входящий звонок, и user_id до сих пор не установлен, то нельзя точно определить кто из
//                        // операторов взял трубку (т.е. на один и тот же номер мог быть привязан любой из операторов)
//                    }

                    $call->accepted_time = time();
                    if(!$call->save(false)) {
                        throw new ErrorException('Не удалось сохранить время ACCEPTED');
                    }

                    $aUsersIds = [$call->user_id];
                    $data = [
                        'is_accepted_call' => true,
                        'call_id' => $call->id,
                    ];
                    SocketDemon::sendOutBrowserMessageInstant('all_site_pages', [''], 'updateIncomingCall', $data, $aUsersIds);

                    break;

                case 'COMPLETED':

                    $call->completed_time = time();
                    $call->is_opening = false;
                    if(!$call->save(false)) {
                        throw new ErrorException('Не удалось сохранить время COMPLETED');
                    }

                    $aUsersIds = [$call->user_id];
                    $data = [
                        'is_completed_call' => true,
                        'call_id' => $call->id,
                        'completed_time' => date('H:i:s', $call->completed_time),
                    ];
                    SocketDemon::sendOutBrowserMessageInstant('all_site_pages', [''], 'updateIncomingCall', $data, $aUsersIds);

                    break;

                case 'CANCELLED':

                    $call->canceled_time = time();
                    $call->is_opening = false;
                    if(!$call->save(false)) {
                        throw new ErrorException('Не удалось сохранить время CANCELLED');
                    }

                    $aUsersIds = [$call->user_id];
                    $data = [
                        'is_canceled_call' => true,
                        'canceled_time' => date('H:i:s', $call->canceled_time),
                        'call_id' => $call->id,
                    ];
                    SocketDemon::sendOutBrowserMessageInstant('all_site_pages', [''], 'updateIncomingCall', $data, $aUsersIds);

                    break;

                default:

                    $msg = '';
                    $msg .= 'время='.date('d.m.Y H:i:s', time()).'<br />';
                    foreach(Yii::$app->request->post() as $key => $val) {
                        $msg .= $key.'='.$val."<br />";
                    }
                    Call::sendErrorEmailToAdmin('unknown type', $msg);


                    throw new BadRequestHttpException('unknown type');
                    break;
            }
        }

        return;
    }


//    public function actions()
//    {
//        $actions = parent::actions();
//        unset($actions['view'], $actions['delete'], $actions['index'], $actions['create'], $actions['update']);
//        //unset($actions['view'], $actions['delete'], $actions['index']);
//
//        $actions['view']['class'] = 'app\modules\api\actions\user\ViewAction';
//        $actions['setlocation']['class'] = 'app\modules\api\actions\user\SetLocationAction';
//        $actions['driverlogin']['class'] = 'app\modules\api\actions\user\DriverLoginAction';
//
//
//        return $actions;
//    }

//    protected function verbs(){
//        return [
//            'view' => ['GET', 'POST'],
//            'setlocation' => ['GET', 'POST'],
//            'driverlogin'=> ['GET', 'POST'],
//        ];
//    }

}
