<?php


?>

<div class="waybill-block">
    <div class="waybill-title waybill-block-toogle">Предварительные итоги рейса <span class="waybill-arrow"><i class="glyphicon glyphicon-menu-down"></i></span></div>
    <div class="waybill-body">
        <div class="row">

            <?php if(!in_array(Yii::$app->session->get('role_alias'), ['warehouse_turnover'])) { ?>

                <div class="col-v-45">
                    <table class="waybill-trips-results-table">
                        <tr>
                            <th>НП</th>
                            <th style="width: 120px;">Рейс</th>
                            <th>М</th>
                            <th>Д</th>
                            <th>С</th>
                            <th>А</th>
                            <th>П</th>
                            <th>БМ</th>
                            <th>Lug</th>
                            <th>Sum</th>
                            <th style="color: red;">БЗ</th>
                        </tr>
                        <?php
                        $start_day_report_trip_transport = null;
                        $end_day_report_trip_transport = null;

                        if($model->trip_transport_start != null) {

                            $start_trip_transport = $model->tripTransportStart;
                            $start_trip = $start_trip_transport->trip;
                            $start_day_report_trip_transport = $start_trip_transport->dayReportTripTransport;
                            ?>
                            <tr>
                                <td><?= $start_trip->direction->sh_name ?></td>
                                <td><?= $start_trip->name ?></td>
                                <td><?= $start_day_report_trip_transport->places_count_sent ?></td>
                                <td><?= $start_day_report_trip_transport->child_count_sent ?></td>
                                <td><?= $start_day_report_trip_transport->student_count_sent ?></td>
                                <td><?= $start_day_report_trip_transport->airport_count_sent  ?></td>
                                <td><?= $start_day_report_trip_transport->prize_trip_count_sent ?></td>
                                <td><?= $start_day_report_trip_transport->is_not_places_count_sent ?></td>
                                <td><?= $start_day_report_trip_transport->suitcase_count_sent ?>Ч, <?= $start_day_report_trip_transport->bag_count_sent ?>С, <?= $start_day_report_trip_transport->oversized_count_sent ?>H</td>
                                <td><?= $start_day_report_trip_transport->proceeds ?></td>
                                <td style="color: red;"><?= intval($start_day_report_trip_transport->no_record) ?></td>
                            </tr>
                        <?php } ?>


                        <?php if($model->trip_transport_end != null) {

                            $end_trip_transport = $model->tripTransportEnd;
                            $end_trip = $end_trip_transport->trip;
                            $end_day_report_trip_transport = $end_trip_transport->dayReportTripTransport;
                            ?>
                            <tr>
                                <td><?= $end_trip->direction->sh_name ?></td>
                                <td><?= $end_trip->name ?></td>
                                <td><?= $end_day_report_trip_transport->places_count_sent ?></td>
                                <td><?= $end_day_report_trip_transport->child_count_sent ?></td>
                                <td><?= $end_day_report_trip_transport->student_count_sent ?></td>
                                <td><?= $end_day_report_trip_transport->airport_count_sent  ?></td>
                                <td><?= $end_day_report_trip_transport->prize_trip_count_sent ?></td>
                                <td><?= $end_day_report_trip_transport->is_not_places_count_sent ?></td>
                                <td><?= $end_day_report_trip_transport->suitcase_count_sent ?>Ч, <?= $end_day_report_trip_transport->bag_count_sent ?>С, <?= $end_day_report_trip_transport->oversized_count_sent ?>H</td>
                                <td><?= $end_day_report_trip_transport->proceeds ?></td>
                                <td style="color: red;"><?= intval($end_day_report_trip_transport->no_record) ?></td>
                            </tr>
                        <?php } ?>

                        <?php if($start_day_report_trip_transport != null && $end_day_report_trip_transport != null) { ?>
                            <tr>
                                <td></td>
                                <td>Итого:</td>
                                <td><?= ($start_day_report_trip_transport->places_count_sent + $end_day_report_trip_transport->places_count_sent) ?></td>
                                <td><?= ($start_day_report_trip_transport->child_count_sent + $end_day_report_trip_transport->child_count_sent ) ?></td>
                                <td><?= ($start_day_report_trip_transport->student_count_sent + $end_day_report_trip_transport->student_count_sent) ?></td>
                                <td><?= ($start_day_report_trip_transport->airport_count_sent + $end_day_report_trip_transport->airport_count_sent)  ?></td>
                                <td><?= ($start_day_report_trip_transport->prize_trip_count_sent + $end_day_report_trip_transport->prize_trip_count_sent) ?></td>
                                <td><?= ($start_day_report_trip_transport->is_not_places_count_sent + $end_day_report_trip_transport->is_not_places_count_sent) ?></td>
                                <td><?= ($start_day_report_trip_transport->suitcase_count_sent + $end_day_report_trip_transport->suitcase_count_sent) ?>Ч, <?= ($start_day_report_trip_transport->bag_count_sent + $end_day_report_trip_transport->bag_count_sent) ?>С, <?= ($start_day_report_trip_transport->oversized_count_sent + $end_day_report_trip_transport->oversized_count_sent) ?>H</td>
                                <td><?= ($start_day_report_trip_transport->proceeds + $end_day_report_trip_transport->proceeds) ?></td>
                                <td style="color: red;"><?= intval($start_day_report_trip_transport->no_record + $end_day_report_trip_transport->no_record) ?></td>
                            </tr>
                        <?php } ?>
                    </table>

                </div>

                <div class="col-v-2">&nbsp;</div>
            <?php } ?>

            <div class="col-v-45">
                <table class="waybill-results-table">
                    <tr>
                        <td>Сумма принятых расходов из выручки:</td>
                        <td><?= $model->accepted_expenses_from_revenue ?> руб.</td>
                    </tr>
                    <tr>
                        <td>Сумма непринятых расходов из выручки:</td>
                        <td><?= $model->not_accepted_expenses_from_revenue ?> руб.</td>
                    </tr>
                    <tr>
                        <td>Входящие требования на общую сумму:</td>
                        <td><?= $model->incoming_requirements ?> руб.</td>
                    </tr>
                    <tr>
                        <td>Итого принятые расходы всех типов:</td>
                        <td><?= $model->accepted_expenses_all_types ?> руб.</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
