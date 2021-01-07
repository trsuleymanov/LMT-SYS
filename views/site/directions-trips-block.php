<?php
/*
 * По сути тут используются одновременно 2 шаблона:
 * рейсы главной страницы ('view' => 'trip_list')
 * и установка рейсов (страница Расстановка) ('view' => 'set_trip_list')
 */

use app\models\Access;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use app\models\Order;
use app\models\OrderStatus;
use app\models\TripTransport;
use app\models\Transport;

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


?>
<?php
if(count($aDirections) > 2) { ?>
    <ul id="directions-menu" elems-count="<?= count($aDirections) ?>" class="nav nav-tabs">
        <?php
        foreach ($aDirections as $key => $aDirection) {
            $direction = $aDirection['direction'];
            ?>
            <li class="<?= ($key < 3 ? 'active' : '') ?>" key="<?= $key ?>">
                <a href="#"><?= $direction->sh_name ?></a>
            </li>
        <?php } ?>
    </ul>
    <?php
}?>

<?php
//echo 'view='.$view;
?>

<div id="directions-block">
<?php
    foreach ($aDirections as $key => $aDirection)
    {
        $direction = $aDirection['direction'];

        if(count($aDirection['trips']) > 0) {
            ?><div class="direction">
                <p class="direction-name-block">
                    <?php
                    if($view == 'trip_list') {
                        echo '<input type="radio" name="direction" value="'.$direction->id .'" />';
                    }else { // $view == 'set_trip_list'
                        echo '<input type="checkbox" name="direction" value="'.$direction->id.'" />';
                    }
                    ?>
                    <span class="direction-name"><?= $direction->sh_name ?></span>
                </p>
                <?php
                //echo "aDirection:<pre>"; print_r($aDirection); echo "</pre>";

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
                    $trip_price = 0;
                    if(isset($aTripsOrders[$trip->id])) {
                        $count_trip_orders = count($aTripsOrders[$trip->id]);
                        foreach ($aTripsOrders[$trip->id] as $order) { // не отмененные заказы
                            $count_fact_places += $order->places_count;
                            $trip_price += $order->price;
                            if ($order->is_confirmed == 1) {
                                $count_confirmed_fact_places += $order->places_count;
                            }
                        }
                    }

                    $status = '';
                    if(!empty($trip->date_sended)) {
                        $status = 'sended';
                    }elseif(!empty($trip->date_issued_by_operator)) {
                        $status = 'issued_by_operator';
                    }elseif(!empty($trip->date_start_sending)) {
                        $status = 'start_sending';
                    }
                    ?>
                    <div class="trip" status="<?= $status ?>" has-free-places="<?= intval($trip->has_free_places) ?>" commercial="<?= $trip->commercial ?>" is-reserv="<?= $trip->is_reserv ?>">
                        <div class="trip-top">
                            <div class="trip-top-left">
                                <span><?= $trip->start_time ?></span> / <span><?= $trip->mid_time ?></span> / <span><?= $trip->end_time ?></span>
                            </div>
                            <div class="trip-top-right"><?= Yii::$app->formatter->asDecimal($trip_price, 0) ?></div>
                        </div>
                        <?php if($view == 'set_trip_list') { ?>
                            <div class="trip-checkbox">
                                <?php if(empty($trip->date_sended)) { ?>
                                    <input type="checkbox" class="merged" direction-id="<?= $direction->id ?>" value="<?= $trip->id ?>" trip_name="<?= $trip->name ?>" />
                                <?php } ?>
                            </div>
                        <?php } ?>
                        <div class="reis">
                            <?php if($view == 'set_trip_list') { ?>
                                <a class="trip_detail_link" title="ОСР - <?= $trip_price ?>" trip-id = <?= $trip->id ?>><?= $trip->name ?></a>
                            <?php }else { // trip_list  ?>
                                <?php // if(!in_array(Yii::$app->session->get('role_alias'), ['graph_operator', 'warehouse_turnover'])) { ?>
                                <?php if (Access::hasUserAccess('trip/trip-orders', 'page_url')) { ?>
                                    <a title="ОСР - <?= $trip_price ?>" href="<?= Url::to(['trip/trip-orders', 'trip_id' => $trip->id]) ?>"><?= $trip->name ?></a>
                                <?php }else { ?>
                                    <a title="ОСР - <?= $trip_price ?>" href="#"><?= $trip->name ?></a>
                                <?php } ?>
                            <?php } ?>
                        </div>
                        <div class="transports-names">
                            <?php
                            if(isset($aTripTransports[$trip->id]) && count($aTripTransports[$trip->id]) > 0) {

                                // сортируем машины в порядке сортировки
                                $aCurrentTripTransports = [];
                                foreach ($aTripTransports[$trip->id] as $trip_transport) {
                                    $aCurrentTripTransports[$trip_transport->sort.'_'.$trip_transport->transport_id] = $trip_transport;
                                }
                                krsort($aCurrentTripTransports);

                                foreach ($aCurrentTripTransports as $trip_transport) {
                                    if(isset($aTransports[$trip_transport->transport_id])) {
                                    ?>
                                        <div class="transport-name <?= $trip_transport->statusClass ?>"
                                             trip_transport_id="<?= $trip_transport->id ?>"
                                             trip_id="<?= $trip_transport->trip_id ?>"><?= $aTransports[$trip_transport->transport_id]->name3 ?></div>
                                    <?php } ?>
                                <?php }
                            }else { ?>
                                &nbsp;
                            <?php } ?>
                        </div>
                        <?php // if(!in_array(Yii::$app->session->get('role_alias'), ['graph_operator', 'warehouse_turnover'])) { ?>
                        <?php if (Access::hasUserAccess('indicators', 'page_part')) { ?>
                            <div class="transports-places" status="<?= ($count_trip_orders <= $count_transports_places && $count_transports_places > 0 ? 'success' : 'error')?>">
                                <div class="place place-col-1" trip-id="<?= $trip->id ?>"><span title="Мест"><?= $count_fact_places ?></span> <span title="Заказов">(<?= $count_trip_orders ?>)</span></div>
                                <div class="place place-col-2" trip-id="<?= $trip->id ?>"><span class="confirmed-places" title="Подтвержденных мест в т/с"><?= $count_confirmed_fact_places ?></span><span title="Мест в т/с">/<?= $count_transports_places ?></span></div>
                            </div>
                        <?php }else { ?>
                            &nbsp;
                        <?php } ?>
                    </div>
                <?php } ?>
            </div><?php
        }
    }
    ?>
</div>
