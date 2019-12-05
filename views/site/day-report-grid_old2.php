<?php
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use app\models\OrderCancellationReason;
use yii\helpers\Url;


$order_cancellation_reasons = OrderCancellationReason::find()->all();

Pjax::begin([
    'enablePushState' => false, // отключаем обновление урла в браузере
]);
?>

<?php

echo GridView::widget([
    'id' => 'trip-transports-grid',
    'dataProvider' => $dayReportDataProvider,
    //'filterModel' => $tripTransportSearchModel,
    //'layout' => '{items}{pager}',
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],

//        [
//            'attribute' => 'transport_id',
//            'label' => 'Транспорт'
//        ],

        [
            'attribute' => 'direction_name_1',
            'label' => 'НПР',
            'content' => function($model) {
                return (!empty($model->direction_name_1) ? $model->direction_name_1 : '');
            },
        ],

        // в колонку объединяется название рейса, время его отправки и кем,
        // сортировка выполняется по значению название рейса
        [
            'attribute' => 'trip_name_1',
            'label' => 'Рейс',
            'content' => function($model) {
                return
                    $model->trip_name_1
                    .(!empty($model->trip_date_sended_1) ? '<br />'.date("H:i d.m.Y", $model->trip_date_sended_1) : '')
                    .(!empty($model->trip_sender_fio_1) ? '<br />'.$model->trip_sender_fio_1 : '');
            }
        ],
        [
            'attribute' => 'transport_car_reg_1',
            'label' => 'Т/с',
            'content' => function($model) {
                if(!empty( $model->transport_car_reg_1)) {
                    return
                        $model->transport_car_reg_1 . ' ' . $model->transport_model_1 . ' (' . $model->transport_places_count_1 . ')'
                        . '<br />Водитель: ' . $model->driver_fio_1
                        . (!empty($model->transport_date_sended_1) ? '<br />Т/с отпр. ' . date("H:i d.m.Y", $model->transport_date_sended_1) . ',<br />' . $model->transport_sender_fio_1 : '');
                }else {
                    return '';
                }
            }
        ],

        [
            'attribute' => 'places_count_sent_1',
            'label' => 'М', //Всего
            'content' => function($model) {
                return ($model->places_count_sent_1 != null ? $model->places_count_sent_1 : '');
            }
        ],
        [
            'attribute' => 'child_count_sent_1',
            'label' => 'Д',
            'content' => function($model) {
                return ($model->child_count_sent_1 != null ? $model->child_count_sent_1 : '');
            }
        ],
        [
            'attribute' => 'student_count_sent_1',
            'label' => 'С',
            'content' => function($model) {
                return ($model->student_count_sent_1 != null ? $model->student_count_sent_1 : '');
            }
        ],
        [
            'attribute' => 'prize_trip_count_sent_1',
            'label' => 'Пр',
            'content' => function($model) {
                return ($model->prize_trip_count_sent_1 != null ? $model->prize_trip_count_sent_1 : '');
            }
        ],
        [
            'attribute' => 'bag_count_sent_1',
            'label' => 'Багаж',
            'content' => function($model) {
                if($model->suitcase_count_sent_1 == null && $model->bag_count_sent_1 == null && $model->oversized_count_sent_1 == null) {
                    return null;
                }else {
                    return $model->suitcase_count_sent_1 . 'Ч, ' . $model->bag_count_sent_1 . 'С, ' . $model->oversized_count_sent_1 . 'Н';
                }
            }
        ],
        [
            'attribute' => 'is_not_places_count_sent_1',
            'label' => 'БМ', //Без места
            'content' => function($model) {
                return ($model->is_not_places_count_sent_1 != null ? $model->is_not_places_count_sent_1 : '');
            }
        ],
        [
            'attribute' => 'proceeds_1',
            'label' => 'Выручка',
            'content' => function($model) {
                return ($model->proceeds_1 != null ? $model->proceeds_1.' руб.' : '');
            }
        ],




        [
            'attribute' => 'direction_name_2',
            'label' => 'НПР',
            'content' => function($model) {
                return (!empty($model->direction_name_2) ? $model->direction_name_2 : '');
            }
        ],
        [
            'attribute' => 'trip_name_2',
            'label' => 'Рейс',
            'content' => function($model) {
                return
                    $model->trip_name_2
                    .(!empty($model->trip_date_sended_2) ? '<br />'.date("H:i d.m.Y", $model->trip_date_sended_2) : '')
                    .(!empty($model->trip_sender_fio_2) ? '<br />'.$model->trip_sender_fio_2 : '');
            }
        ],
        [
            'attribute' => 'transport_car_reg_2',
            'label' => 'Т/с',
            'content' => function($model) {

                if(!empty( $model->transport_car_reg_2)) {
                    return
                        $model->transport_car_reg_2.' '.$model->transport_model_2.' ('.$model->transport_places_count_2.')'
                        .'<br />Водитель: '.$model->driver_fio_2
                        .(!empty($model->transport_date_sended_2) ? '<br />Т/с отпр. '.date("H:i d.m.Y", $model->transport_date_sended_2).',<br />'.$model->transport_sender_fio_2 : '');
                }else {
                    return '';
                }
            }
        ],

        [
            'attribute' => 'places_count_sent_2',
            'label' => 'М', //Всего
            'content' => function($model) {
                return ($model->places_count_sent_2 != null ? $model->places_count_sent_2 : '');
            }
        ],
        [
            'attribute' => 'child_count_sent_2',
            'label' => 'Д',
            'content' => function($model) {
                return ($model->child_count_sent_2 != null ? $model->child_count_sent_2 : '');
            }
        ],
        [
            'attribute' => 'student_count_sent_2',
            'label' => 'С',
            'content' => function($model) {
                return ($model->student_count_sent_2 != null ? $model->student_count_sent_2 : '');
            }
        ],
        [
            'attribute' => 'prize_trip_count_sent_2',
            'label' => 'Пр',
            'content' => function($model) {
                return ($model->prize_trip_count_sent_2 != null ? $model->prize_trip_count_sent_2 : '');
            }
        ],
        [
            'attribute' => 'bag_count_sent_2',
            'label' => 'Багаж',
            'content' => function($model) {
                if($model->suitcase_count_sent_2 == null && $model->bag_count_sent_2 == null && $model->oversized_count_sent_2 == null) {
                    return null;
                }else {
                    return $model->suitcase_count_sent_2 . 'Ч, ' . $model->bag_count_sent_2 . 'С, ' . $model->oversized_count_sent_2 . 'Н';
                }
            }
        ],
        [
            'attribute' => 'is_not_places_count_sent_2',
            'label' => 'БМ', //Без места
            'content' => function($model) {
                return ($model->is_not_places_count_sent_2 != null ? $model->is_not_places_count_sent_2 : '');
            }
        ],
        [
            'attribute' => 'proceeds_2',
            'label' => 'Выручка',
            'content' => function($model) {
                return ($model->proceeds_2 != null ? $model->proceeds_2.' руб.' : '');
            }
        ],
        [
            'attribute' => 'total_proceeds',
            'label' => 'Финальный расчет',
            'content' => function($model) {
                return $model->total_proceeds.' руб.';
            }
        ],
    ],
]);


/*

echo GridView::widget([
    'id' => 'trip-transports-grid',
    'dataProvider' => $dayReportDataProvider,
    //'filterModel' => $tripTransportSearchModel,
    'layout' => '{items}{pager}',
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],

        [
            'attribute' => 'direction_name',
            'label' => 'НПР',
        ],

        // в колонку объединяется название рейса, время его отправки и кем,
        // сортировка выполняется по значению название рейса
        [
            'attribute' => 'trip_name',
            'label' => 'Рейс',
            'content' => function($model) {
                return
                    $model->trip_name
                    .(!empty($model->trip_date_sended) ? '<br />'.date("H:i d.m.Y", $model->trip_date_sended) : '')
                    .(!empty($model->trip_sender_fio) ? '<br />'.$model->trip_sender_fio : '');
            }
        ],
        [
            'attribute' => 'transport_car_reg',
            'label' => 'Т/с',
            'content' => function($model) {
                return
                    $model->transport_car_reg.' '.$model->transport_model.' ('.$model->transport_places_count.')'
                    .'<br />Водитель: '.$model->driver_fio
                    .(!empty($model->transport_date_sended) ? '<br />Т/с отпр. '.date("H:i d.m.Y", $model->transport_date_sended).',<br />'.$model->transport_sender_fio : '');
            }
        ],
        [
            'attribute' => 'places_count_sent',
            'label' => 'М', //Всего
        ],
        [
            'attribute' => 'child_count_sent',
            'label' => 'Д',
        ],
        [
            'attribute' => 'student_count_sent',
            'label' => 'С',
        ],
        [
            'attribute' => 'prize_trip_count_sent',
            'label' => 'Пр',
        ],
        [
            'attribute' => 'bag_count_sent',
            'label' => 'Багаж',
            'content' => function($model) {
                return $model->suitcase_count_sent.'Ч, '.$model->bag_count_sent.'С, '.$model->oversized_count_sent.'Н';
            }
        ],
        [
            'attribute' => 'is_not_places_count_sent',
            'label' => 'БМ', //Без места
        ],
        [
            'attribute' => 'proceeds',
            'label' => 'Выручка',
        ],
        [
            'attribute' => 'direction_name',
            'label' => 'НПР',
            'content' => function($model) {

            }
        ],
    ],
]);
*/
Pjax::end();
?>
<?php
if(!in_array(Yii::$app->session->get('role_alias'), ['editor', 'manager'])) {
    $num = 1;
    foreach ($order_cancellation_reasons as $order_cancellation_reason) {
        echo $num . '. <a href="' . Url::to(['/order/cancellation-reason-orders', 'date' => $date, 'cancellation_reason_id' => $order_cancellation_reason->id]) . '" target="_blank">Заказы, удаленные со статусом "' . $order_cancellation_reason->name . '"</a>;<br />';
        $num++;
    }
}
?>
<br />
