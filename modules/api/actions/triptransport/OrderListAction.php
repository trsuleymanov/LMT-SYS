<?php

namespace app\modules\api\actions\triptransport;

use app\models\Driver;
use app\models\Order;
use app\models\TripTransport;
use Yii;
use app\models\User;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;
use app\models\Trip;
use app\models\OrderStatus;

/*
 * Список заказов на рейсе-машине
 */
class OrderListAction extends \yii\rest\Action
{
    public $modelClass = '';

    public function run()
    {
        $user = User::find()->where(['id' => Yii::$app->user->id])->one();
        if($user == null) {
            throw new ForbiddenHttpException('Пользователь не найден');
        }

        $driver = Driver::find()->where(['user_id' => $user->id])->one();
        if($driver == null) {
            throw new ForbiddenHttpException('Водитель не найден');
        }

        $access_key = Yii::$app->getRequest()->getBodyParam('transport_access_key');
        if(strlen($access_key) != 10) {
            throw new ForbiddenHttpException('Идентификатор должен быть длиной 10 символов');
        }
        $trip_transport = TripTransport::find()
            ->where(['access_key' => $access_key])
            ->andWhere(['driver_id' => $driver->id])
            ->andWhere(['date_sended' => NULL])
            ->one();
        if($trip_transport == null) {
            throw new ForbiddenHttpException('Машина на рейсе не найдена');
        }


        $orders = $trip_transport->factOrders;
        $aOrders = [];
        foreach($orders as $order) {
            // колонки: Фамилия, мест/д/с/п, Куда, сумма оплаты
            if(!empty($order->time_sat)) {
                $aOrders[] = [
                    'id' => $order->id,
                    'client_name' => $order->client->name,
                    'client_mobile' => $order->client->mobile_phone,
                    'places_count' => intval($order->places_count),
                    'student_count' => intval($order->student_count),
                    'child_count' => intval($order->child_count),
                    'prize_trip_count' => intval($order->prize_trip_count),
                    'yandex_point_to_name' => $order->yandex_point_to_name,
                    'price' => $order->price
                ];
            }
        }


        return $aOrders;
    }
}
