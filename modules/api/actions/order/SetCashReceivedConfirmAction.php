<?php

namespace app\modules\api\actions\order;

use app\models\Driver;
use app\models\Order;
use app\models\SocketDemon;
use Yii;
use app\models\User;
use yii\web\ForbiddenHttpException;

/*
 * Подтверждение что приложение получило данные что "Деньги за заказ получены" :)    и проведение фискализации заказа
 */
class SetCashReceivedConfirmAction extends \yii\rest\Action
{
    public $modelClass = '';

    /**
     * @throws ForbiddenHttpException
     * @throws \yii\base\ErrorException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
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


        // оплачиваем заказ с выдачей чеков
        $aFields = [
            'payment_source' => 'application'
        ];
        if($order->setPay($aFields, true)) {

            $order->setField('cash_received_time', time());
            $order->setField('cash_received_by_user_id', Yii::$app->user->id);
        }

        if($order->trip_id > 0) {
            // передаем сообщение в браузер
            SocketDemon::sendOutBrowserMessageInstant('/trip/trip-orders', ['trip_id' => $order->trip_id], 'updateTripOrdersPage()', []);
        }

        return;
    }
}
