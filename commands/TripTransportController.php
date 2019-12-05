<?php

namespace app\commands;

use app\models\Order;
use app\models\OrderStatus;
use app\models\TripTransport;
use yii\console\Controller;
use Yii;
use yii\helpers\ArrayHelper;


class TripTransportController extends Controller
{
    // поиск машины которые были отправлены 4 и более часа назад, и удаление этим машинам идентификаторов доступа
    // команда: php yii trip-transport/update-access-keys
    public function actionUpdateAccessKeys()
    {
        $trip_transports = TripTransport::find()
            ->where(['not', ['access_key' => null]])
            ->andWhere(['not', ['access_key' => '']])
            ->andWhere(['status_id' => 1])
            ->andWhere(['<', 'date_sended', time() - 4*3600])
            ->all();

        //echo "trip_transports:<pre>"; print_r($trip_transports); echo "</pre>";
        foreach($trip_transports as $trip_transport) {
            echo "id=".$trip_transport->id."\n";

            $trip_transport->setField('access_key', '');
            $driver = $trip_transport->driver;
            if($driver != null && $driver->magic_device_code_id > 0) {
                $driver->setField('magic_device_code_id', '');
            }
        }

        echo "скрипт отработал\n";
    }

    /*
    // всем отмененным заказам которые привязаны к отправленным машинам устанавливаю значение date_sended
    // равное времени отправки машины
    // команда: php yii trip-transport/update-order-date-sended
    public function actionUpdateOrderDateSended()
    {
        ini_set('memory_limit', '-1');
        set_time_limit(0);

        // date_sended
        $canceled_order_status = OrderStatus::getByCode('canceled');
        $sent_order_status = OrderStatus::getByCode('sent');
        $orders = Order::find()
            ->where(['status_id' => [$canceled_order_status->id, $sent_order_status->id]])
            ->andWhere(['>', 'fact_trip_transport_id', 0])
            ->all();


        $aSendedTripTransportsOrders = [];
        foreach($orders as $order) {
            $aSendedTripTransportsOrders[$order->fact_trip_transport_id][$order->id] = $order;
        }

        $trip_transports = TripTransport::find()
            ->where(['id' => ArrayHelper::map($orders, 'fact_trip_transport_id', 'fact_trip_transport_id')])
            ->andWhere(['>', 'date_sended', 0])
            ->all();

        foreach($trip_transports as $trip_transport) {
            if(isset($aSendedTripTransportsOrders[$trip_transport->id]) && count($aSendedTripTransportsOrders[$trip_transport->id]) > 0) {
                $aOrdersIds = ArrayHelper::map($aSendedTripTransportsOrders[$trip_transport->id], 'id', 'id');

                $sql = 'UPDATE `order` SET `date_sended`="'.$trip_transport->date_sended.'" WHERE id IN ('.implode(',', $aOrdersIds).')';
                Yii::$app->db->createCommand($sql)->execute();
            }
        }
    }
*/

}
