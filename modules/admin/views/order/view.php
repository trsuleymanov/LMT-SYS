<?php

use app\models\User;
use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\Order;
use yii\helpers\Url;

$this->title = 'Просмотр заказа: &laquo;' . $model->id . '&raquo;';
$this->params['breadcrumbs'][] = ['label' => 'Заказы', 'url' => ['/admin/order/index']];
$this->params['breadcrumbs'][] = ['label' => 'Заказ ' . $model->id, 'url' => ['/admin/order/update', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Просмотр заказ';

?>

<div class="box box-solid">

    <div class="box-header with-border">
        <h3 class="box-title">
            <i class="fa fa-address-book-o"></i>
            Заказ
        </h3>
        <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse">
                <i class="fa fa-minus"></i>
            </button>
        </div>
    </div>

    <div class="box-body">
        <?php

        echo DetailView::widget([
            'model' => $model,
            'attributes' => [
                'id',
                [
                    'attribute' => 'status_id',
                    'value' => ($model->status_id > 0 ? $model->status->name.', ' : '').' '.($model->is_confirmed == 1 ? 'подтвержден, ' : 'не подтвержден, ').' ВРПТ - '.(!empty($model->time_confirm) ? date('d.m.Y H:i', $model->time_confirm) : 'нет')
                ],
                [
                    'attribute' => 'date',
                    'label' => 'Дата заказа',
                    'value' =>
                        ($model->date > 0 ? date('d.m.Y', $model->date).', ' : '').' '
                        .($model->direction_id > 0 ? $model->direction->sh_name . ', ' : '').' '
                        .($model->trip_id > 0 ? $model->trip->name : 'рейс '.$model->trip_id.' не существует')
                ],
                [
                    'attribute' => 'informer_office_id',
                    'label' => 'Источник',
                    'value' => ($model->informer_office_id > 0 ? $model->informerOffice->name : '')
                ],
                [
                    'attribute' => 'client_id',
                    'format' => 'raw',
                    'value' => ($model->client_id > 0 ? $model->client->name.', <a target="_blank" href="'.Url::to(['/client/view', 'id' => $model->client_id]).'">'.$model->client->mobile_phone.'</a>' : '')
                ],
                [
                    'attribute' => 'is_not_places',
                    'label' => 'Струкрура заказа',
                    'value' => ($model->is_not_places == 1 ? 'БМ/' : '').intval($model->places_count).'М/'
                        .intval($model->student_count).'С/'.intval($model->child_count).'Д/'
                        .intval($model->prize_trip_count).'Приз ('.intval($model->suitcase_count).'Ч/'
                        .intval($model->bag_count).'С/'.intval($model->oversized_count).'Н)'
                ],
                [
                    'attribute' => 'price use_fix_price',
                    'label' => 'Цена/фиксированная цена',
                    'value' => intval($model->price).'/'.($model->use_fix_price == 1 ? 'да' : 'нет')
                ],
                [
                    'attribute' => 'fact_trip_transport_id',
                    'label' => 'Т/с',
                    'value' => ($model->fact_trip_transport_id > 0 ? $model->factTripTransport->transport->name4 : '')
                ],
                [
                    'attribute' => 'time_sat',
                    'value' => ($model->time_sat > 0 ? date('d.m.Y H:i', $model->time_sat) : '')
                ],
                [
                    'attribute' => 'time_satter_user_id',
                    'value' => ($model->time_satter_user_id > 0 ? User::find()->where(['id' => $model->time_satter_user_id])->one()->username : '')
                ],
                [
                    'attribute' => 'confirmed_time_sat',
                    'value' => ($model->confirmed_time_sat > 0 ? date('d.m.Y H:i', $model->confirmed_time_sat) : '')
                ],
                [
                    'attribute' => 'confirmed_time_satter_user_id',
                    'value' => ($model->confirmed_time_satter_user_id > 0 ? User::find()->where(['id' => $model->confirmed_time_satter_user_id])->one()->username : '')
                ],


                [
                    'attribute' => 'created_at',
                    'label' => 'Время создания (ВПЗ)',
                    'value' => date('d.m.Y H:i', $model->created_at).' '
                        .($model->first_writedown_clicker_id > 0 ? $model->firstWritedownClicker->fullname : '')
                ],
                [
                    'attribute' => 'updated_at',
                    'value' => ($model->updated_at > 0 ? date('d.m.Y H:i', $model->updated_at) : '')
                ],
                [
                    'attribute' => 'last_writedown_click_time',
                    'label' => ' Последнее нажатие кнопки Записать',
                    'value' => ($model->last_writedown_click_time > 0 ? date('d.m.Y H:i', $model->last_writedown_click_time) : '').' '
                                .($model->last_writedown_clicker_id > 0 ? $model->lastWritedownClicker->fullname : '')
                ],
                [
                    'attribute' => 'first_confirm_click_time',
                    'label' => 'Первичное нажатия кнопки Подтвердить',
                    'value' => ($model->first_confirm_click_time > 0 ? date('d.m.Y H:i', $model->first_confirm_click_time) : '').' '
                            .($model->first_confirm_clicker_id > 0 ? $model->firstConfirmClicker->fullname : '')
                ],
                [
                    'attribute' => 'last_confirm_click_time',
                    'label' => 'Последнеее нажатие кнопки Подтвердить',
                    'value' =>
                        ($model->last_confirm_click_time > 0 ? date('d.m.Y H:i', $model->last_confirm_click_time) : '').' '
                        .($model->last_confirm_clicker_id > 0 ? $model->lastConfirmClicker->fullname : '')
                ],

                [
                    'attribute' => 'cancellation_reason_id',
                    'label' => 'Статус отмены заказа',
                    'value' =>
                        ($model->cancellation_click_time > 0 ? date('d.m.Y H:i', $model->cancellation_click_time) : '') . ' '
                        .($model->cancellation_reason_id > 0 ? $model->cancellationReason->name : '').' '
                        .($model->cancellation_clicker_id > 0 ? $model->cancellationClicker->fullname : '')

                ],
                [
                    'attribute' => 'street_id_from',
                    'value' => ($model->streetFrom != null ? $model->streetFrom->name : '')
                ],
                [
                    'attribute' => 'point_id_from',
                    'label' => 'Точка (откуда)',
                    'value' => ($model->pointFrom != null ? $model->pointFrom->name : '')
                ],
                [
                    'attribute' => 'client_position_from_lat',
                    'value' => ($model->client_position_from_lat > 0 ? $model->client_position_from_lat : '')
                ],
                [
                    'attribute' => 'client_position_from_long',
                    'value' => ($model->client_position_from_lat > 0 ? $model->client_position_from_lat : '')
                ],
                [
                    'attribute' => 'yandex_point_from_id',
                    'label' => 'Яндекс-точка откуда',
                    'value' => ($model->yandex_point_from_id > 0 ? $model->yandex_point_from_id : '')
                ],
                'yandex_point_from_name',
                'yandex_point_from_lat',
                'yandex_point_from_long',
                'time_air_train_arrival',
                [
                    'attribute' => 'street_id_to',
                    'value' => ($model->streetTo != null ? $model->streetTo->name : '')
                ],
                [
                    'attribute' => 'point_id_to',
                    'value' => ($model->pointTo != null ? $model->pointTo->name : '')
                ],
                [
                    'attribute' => 'yandex_point_to_id',
                    'label' => 'Яндекс-точка куда',
                    'value' => ($model->yandex_point_to_id > 0 ? $model->yandex_point_to_id : '')
                ],
                'yandex_point_to_name',
                'yandex_point_to_lat',
                'yandex_point_to_long',
                'time_air_train_departure',

                'comment',
                [
                    'attribute' => 'additional_phone_1',
                    'label' => 'Дополнительные телефоны',
                    'value' =>
                        (!empty($model->additional_phone_1) ? $model->additional_phone_1.' ' : '')
                        .(!empty($model->additional_phone_2) ? $model->additional_phone_2.' ' : '')
                        .(!empty($model->additional_phone_3) ? $model->additional_phone_3.' ' : '')
                ],
                [
                    'attribute' => 'radio_confirm_now',
                    'value' => ($model->radio_confirm_now > 0 ? $model->radioConfirmNow[$model->radio_confirm_now] : '')
                ],
                [
                    'attribute' => 'radio_group_1',
                    'value' => ($model->radio_group_1 > 0 ? $model->clearRadioGroup1[$model->radio_group_1] : '')
                ],
                [
                    'attribute' => 'radio_group_2',
                    'value' => ($model->radio_group_2 > 0 ? $model->radioGroup2[$model->radio_group_2] : '')
                ],
                [
                    'attribute' => 'radio_group_3',
                    'value' => ($model->radio_group_3 > 0 ? $model->radioGroup3[$model->radio_group_3] : '')
                ],
                [
                    'attribute' => 'confirm_selected_transport',
                    'value' => ($model->confirm_selected_transport == 1 ? 'да' : '')
                ],
                [
                    'attribute' => 'confirm_selected_transport',
                    'value' => ($model->confirm_selected_transport == 1 ? 'да' : '')
                ],
                [
                    'attribute' => 'has_penalty',
                    'value' => ($model->has_penalty == 1 ? 'да' : 'нет')
                ],
                [
                    'attribute' => 'penalty_comment',
                    'value' => $model->penalty_comment.' '.(!empty($model->penalty_author_id) ? $model->penaltyAuthor->username : '').' '.(!empty($model->penalty_time) ? date('d.m.Y H:i', $model->penalty_time) : '')  // {комментарий}{кем сделан}{время, дата комментария}
                ],
                [
                    'attribute' => 'relation_order_id',
                    'format' => 'raw',
                    'value' => ($model->relation_order_id > 0 ? Html::a($model->relation_order_id, Url::to(['/admin/order/view', 'id' => $model->relation_order_id])) : 'нет')
                ],

            ],
        ]) ?>
    </div>
</div>