<?php
namespace app\modules\api\actions\driver;

use app\models\Call;
use app\models\Driver;
use app\models\DriverOperatorChat;
use app\models\Order;
use app\models\SocketDemon;
use app\models\Trip;
use app\models\TripTransport;
use app\models\User;
use Yii;
use yii\base\ErrorException;
use yii\web\ForbiddenHttpException;

class SetMessageToOperatorAction extends \yii\rest\Action
{
    public $modelClass = '';

    public function run()
    {
        // "msg_type=" + msg_type + "&msg_id=" + msg_id + "&msg=" + msg + "&order_id=" + order_id + "&trip_transport_id=" + server_trip_transport_id;
        $msg_type = Yii::$app->getRequest()->getBodyParam('msg_type');
        $msg_id = Yii::$app->getRequest()->getBodyParam('msg_id');
        $msg = Yii::$app->getRequest()->getBodyParam('msg');
        $order_id = Yii::$app->getRequest()->getBodyParam('order_id');
        $trip_transport_id = Yii::$app->getRequest()->getBodyParam('trip_transport_id');

        if($msg_type == 'message') {

            $order = null;
            if ($order_id > 0) {
                $order = Order::find()->where(['id' => $order_id])->one();
                if ($order == null) {
                    throw new ForbiddenHttpException('Заказ не найден');
                }
            }

            $trip_transport = TripTransport::find()->where(['id' => $trip_transport_id])->one();
            if ($trip_transport == null) {
                throw new ForbiddenHttpException('Машино-рейс не найден');
            }

            $trip = $trip_transport->trip;
            if ($trip == null) {
                throw new ForbiddenHttpException('Рейс не найден');
            }

            $direction = $trip->direction;
            if ($direction == null) {
                throw new ForbiddenHttpException('Направление не найдено');
            }

            $transport = $trip_transport->transport;
            if ($transport == null) {
                throw new ForbiddenHttpException('Машина не найдена');
            }

            $chat = new DriverOperatorChat();
            $chat->trip_transport_id = $trip_transport->id;
            $chat->driver_id = $trip_transport->driver_id;
            $chat->message_from_driver = $msg;
            $chat->message_from_driver_at = time();
            if (!$chat->save(false)) {
                throw new ErrorException('Не удалось сохранить сообщение');
            }


            // пример заголовка:
            // АК 3:40: сообщение от ФД 564 - пассажир "Федоров"
            $title = $direction->sh_name . ' ' . $trip->name . ': сообщение от ' . $transport->sh_model . ' ' . $transport->car_reg
                . ($order != null ? ' - пассажир "' . $order->client_name . '" ' : '');


            // передаем сообщение в браузер
            $data = [
                'msg' => $msg,
                'title' => $title,
                //'trip_transport_id' => $trip_transport->id
                'chat_id' => $chat->id
            ];
            $aUsersIds = []; // всем пользователям уходит сообщение
            SocketDemon::sendOutBrowserMessageInstant('all_site_pages', [''], 'sendMsgToOperator', $data, $aUsersIds);

            return; // 200

        }elseif($msg_type == 'accept_receiving_msg') {

            if($msg_id > 0) {

                $chat = DriverOperatorChat::find()->where(['id' => $msg_id])->one();
                if ($chat == null) {
                    throw new ForbiddenHttpException('Подтверждаемое сообщение в базе не найдено');
                }

                $chat->setField('driver_is_read_at', time());

                // закрываем сообщение
                $data = [
                    'chat_id' => $chat->id
                ];
                $aUsersIds = []; // всем пользователям уходит сообщение
                SocketDemon::sendOutBrowserMessageInstant('all_site_pages', [''], 'closeMessage', $data, $aUsersIds);
            }

            return; // 200


        }else {
            throw new ForbiddenHttpException('Неизвестный системе тип сообщения');
        }
    }
}