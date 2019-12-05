<?php
use app\models\Order;
use app\models\OrderStatus;
use app\models\Transport;
use app\models\TripTransport;
use yii\helpers\ArrayHelper;

$canceled_order_status = OrderStatus::getByCode('canceled');

$aTripsId = [];
$aTripTransports = [];
$aTransportsId = [];
$aTransports = [];

foreach($aDirections as $key => $aDirection) {
    foreach($aDirection['trips'] as $trip) {
        $aTripsId[] = $trip->id;
    }
}

if(count($aTripsId) > 0) {
    $trip_transports = TripTransport::find()->where(['trip_id' => $aTripsId])->all();

    foreach ($trip_transports as $trip_transport) {
        $aTripTransports[$trip_transport->trip_id][] = $trip_transport;
        $aTransportsId[$trip_transport->transport_id] = $trip_transport->transport_id;
    }
}

if(count($aTransportsId) > 0) {
    $transports = Transport::find()->where(['id' => $aTransportsId])->all();
    $aTransports = ArrayHelper::index($transports, 'id');
}

$aTripsOrders = [];
if(count($aTripsId) > 0) {
    $trips_orders = Order::find()
        ->where(['trip_id' => $aTripsId])
        ->andWhere(['!=', 'status_id', $canceled_order_status->id])
        ->all();
    foreach($trips_orders as $order) {
        $aTripsOrders[$order->trip_id][] = $order;
    }
}


foreach ($aDirections as $key => $aDirection)
{
    $direction = $aDirection['direction'];
    if(count($aDirection['trips']) > 0)
    { ?>

        <div class="<?= (($key == count($aDirections) - 1) ? 'col-tobus-center-right' : 'col-tobus-center-left') ?>">
            <p class="sh_route"><input name="direction" value="<?= $direction->id ?>" type="checkbox"> <span class="direction-name"><?= $direction->sh_name ?></span></p>
            <table class="info-list <?= (($key == count($aDirections) - 1) ? 'info-list-right' : '') ?>">
                <tbody>
                <?php
                foreach ($aDirection['trips'] as $trip)
                {
                    $count_transports_places = 0;
                    if(isset($aTripTransports[$trip->id]) && count($aTripTransports[$trip->id]) > 0) {
                        foreach ($aTripTransports[$trip->id] as $trip_transport) {
                            if(isset($aTransports[$trip_transport->transport_id])) {
                                $count_transports_places += $aTransports[$trip_transport->transport_id]->places_count;
                            }
                        }
                    }


                    $count_fact_places = 0;
                    $count_confirmed_fact_places = 0;
                    $count_trip_orders = 0;
                    if(isset($aTripsOrders[$trip->id])) {
                        $count_trip_orders = count($aTripsOrders[$trip->id]);
                        foreach ($aTripsOrders[$trip->id] as $order) { // не отмененные заказы
                            $count_fact_places += $order->places_count;
                            if ($order->is_confirmed == 1) {
                                $count_confirmed_fact_places += $order->places_count;
                            }
                        }
                    }

                    ?>
                    <tr class="trip" is-start-sending="<?= !empty($trip->date_start_sending) ?>" is-sended="<?= !empty($trip->date_sended) ?>" commercial="<?= $trip->commercial ?>">
                        <td rowspan="3" class="span1"></td>
                        <td class="span2 points"><?= $trip->start_time ?></td>
                        <td>
                            <?php if(empty($trip->date_sended)) { ?>
                                <input type="checkbox" class="merged" direction-id="<?= $direction->id ?>" value="<?= $trip->id ?>" trip_name="<?= $trip->name ?>" />
                            <?php } ?>
                        </td>
                        <td rowspan="3" class="reis_name span5">
                            <div class="reis_name_content">
                                <a href="#" class="trip_detail_link" trip-id="<?= $trip->id ?>"><?= $trip->name ?></a>
                                <?php if (empty($trip->date_sended)): ?>
                                    <span class="add_transport_plus" trip-id="<?= $trip->id ?>"><i class="glyphicon glyphicon-plus-sign"></i></span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td rowspan="3" class="span2">
                            <div class="transports-places"
                                 status="<?= ($count_trip_orders <= $count_transports_places && $count_transports_places > 0 ? 'success' : 'error') ?>">
                                <span title="Мест"><?= $count_fact_places ?></span>(<span
                                    title="Заказов"><?= $count_trip_orders ?></span>)-<span
                                    title="Подтвержденных мест в т/с"><?= $count_confirmed_fact_places ?></span>/<span
                                    title="Мест в т/с"><?= $count_transports_places ?></span>
                            </div>
                        </td>
                        <td rowspan="3" class="span2 qwe">
                            <?php
                            if(isset($aTripTransports[$trip->id])) {

                                // сортируем машины в порядке сортировки
                                $aCurrentTripTransports = [];
                                foreach ($aTripTransports[$trip->id] as $trip_transport) {
                                    $aCurrentTripTransports[$trip_transport->sort.'_'.$trip_transport->transport_id] = $trip_transport;
                                }
                                krsort($aCurrentTripTransports);


                                foreach($aCurrentTripTransports as $trip_transport) {
                                    if(isset($aTransports[$trip_transport->transport_id])) {
                                        ?>
                                        <div class="trip_transport <?= $trip_transport->statusClass ?>"
                                             trip_transport_id="<?= $trip_transport->id ?>"
                                             trip_id="<?= $trip_transport->trip_id ?>">
                                            <span><?= $aTransports[$trip_transport->transport_id]->name3 ?></span>
                                        </div>
                                        <?php
                                    }
                                }
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="points"><?= $trip->mid_time ?></td>
                    </tr>
                    <tr>
                        <td class="points"><?= $trip->end_time ?></td>
                    </tr>
                    <tr class="empty_tr"></tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
        <?php
    }
}?>