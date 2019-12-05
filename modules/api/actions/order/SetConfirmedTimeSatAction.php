<?php

namespace app\modules\api\actions\order;

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
 * Установка заказу состояния подтверждения посаженности пассажиров
 */
class SetConfirmedTimeSatAction extends \yii\rest\Action
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

        $order_id = Yii::$app->getRequest()->getBodyParam('order_id');
        $order = Order::find()->where(['id' => $order_id])->one();
        if($order == null) {
            throw new ForbiddenHttpException('Заказ не найден');
        }

        $trip_transport = $order->factTripTransport;
        if($trip_transport == null) {
            throw new ForbiddenHttpException('Привязанный к заказу транспорт не найден');
        }
        if($trip_transport->driver_id != $driver->id) {
            throw new ForbiddenHttpException('Данный заказ обслувает другой водитель');
        }
        if(empty($order->time_sat)) {
            throw new ForbiddenHttpException('Данный заказ не был предварительно посажен');
        }


        $order->setField('confirmed_time_sat', time());
        $order->setField('confirmed_time_satter_user_id', $user->id);


        return [
            'confirmed_fact_places_count' => $trip_transport->confirmFactPlacesCount,
            //'fact_places_count' => $trip_transport->factPlacesCount,
            'transport_places_count' => $trip_transport->transport->places_count
        ];
    }
}
