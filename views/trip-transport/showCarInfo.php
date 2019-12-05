<?php

use app\models\Setting;
use yii\helpers\Html;
use app\widgets\SelectWidget;
use yii\web\JsExpression;

?>

<div id="car-info-form" class="form-data" trip_transport_id="<?= $trip_transport->id ?>" driver_id="<?= $trip_transport->driver_id ?>">

	<div class="row">
		<div class="col-sm-6 form-group form-group-sm">
			<div class="row"><div class="col-sm-12" id="transport-info" transport-id="<?= $trip_transport->transport_id ?>">Автомобиль</div></div><br />
			<div class="row">
				<div class="col-sm-6"><b>Модель:</b></div><div class="col-sm-6 no-left-padding"><?= $trip_transport->transport->model ?></div>
			</div>
			<div class="row">
				<div class="col-sm-6"><b>Цвет:</b></div><div class="col-sm-6 no-left-padding"><?= $trip_transport->transport->color ?></div>
			</div>
			<div class="row">
				<div class="col-sm-6"><b>Рег. номер:</b></div><div class="col-sm-6 no-left-padding"><?= $trip_transport->transport->car_reg ?></div>
			</div>
			<div class="row">
				<div class="col-sm-6"><b>Кол-во мест:</b></div><div class="col-sm-6 no-left-padding"><?= $trip_transport->transport->places_count ?></div>
			</div>
		</div>
		<div class="col-sm-6 form-group form-group-sm">
			<div class="row"><div class="col-sm-12 no-left-padding">Водитель</div></div><br />
			<div class="row">
				<div class="col-sm-5 no-left-padding"><b>ФИО:</b></div><div class="col-sm-7 no-left-padding"><?= (!empty($trip_transport->driver_id) ? $trip_transport->driver->fio : 'не установлен' )?></div>
			</div>
			<?php if(!empty($trip_transport->driver_id)) { ?>
				<div class="row">
					<div class="col-sm-5 no-left-padding"><b>Сот. телефон:</b></div><div class="col-sm-7 no-left-padding"><span class="call-phone-button" phone="<?= $trip_transport->driver->mobile_phone ?>"><?= Setting::changeShowingPhone($trip_transport->driver->mobile_phone, 'show_short_drivers_phones') ?></span></div>
				</div>
				<div class="row">
					<div class="col-sm-5 no-left-padding"><b>Дом. телефон:</b></div><div class="col-sm-7 no-left-padding"><span class="call-phone-button" phone="<?= $trip_transport->driver->home_phone ?>"><?= Setting::changeShowingPhone($trip_transport->driver->home_phone, 'show_short_drivers_phones') ?></span></div>
				</div>
			<?php } ?>
		</div>
	</div>

	<div class="row">
		<div class="col-sm-12">Текущее состояние</div>
	</div>

	<div class="row" style="margin-top: 10px;">
		<div class="col-sm-12">
			<b>Поставлен на рейс:</b> <?= date('d.m.Y H:i', $trip_transport->set_date_time);?> пользователем <?= $trip_transport->setUser->fio ?>
		</div>
	</div>

	<div class="row">
		<div class="col-sm-12">
			<?php if($trip_transport->confirmed == 1): ?>
				<b>Подтверждён:</b> <?= date('d.m.Y H:i', $trip_transport->confirmed_date_time); ?> пользователем <?= $trip_transport->confirmedUser->fio ?>
			<?php else: ?>
				Транспорт не подтверждён
			<?php endif; ?>
		</div>
	</div>

	<div class="row">
		<div class="col-sm-12">
			<?php if($trip_transport->date_sended > 0): ?>
				<b>Отправлен:</b> <?= date('d.m.Y H:i', $trip_transport->date_sended); ?> <?= ($trip_transport->sender != null ? 'пользователем '.$trip_transport->sender->fio : '') ?>
			<?php else: ?>
				Транспорт не отправлен
			<?php endif; ?>
		</div>
	</div>

	<?php if($trip_transport->access_key > 0) { ?>
	<div class="row">
		<div class="col-sm-12">
			Выдан идентификатор: <a href="#" trip-transport-id="<?= $trip_transport->id ?>" class="show-driver-position"><?= substr($trip_transport->access_key, 0, 4) ?>-<?= substr($trip_transport->access_key, 4, 4) ?>-<?= substr($trip_transport->access_key, 8, 2) ?></a>
		</div>
	</div>
	<?php } ?>

	<br />
	<div class="row trip-transport-row">
		<?php if(empty($trip_transport->date_sended) && in_array(Yii::$app->session->get('role_alias'), ['root', 'admin', 'editor'])) { ?>
			<div class="col-sm-2" style="margin-top: 5px;"><b>Водитель:</b></div>
			<div class="col-sm-5">
				<div class="form-group"><?php //
					//echo Html::dropDownList('driver_id', $trip_transport->driver_id, $driver_list, ['class' => 'form-control change_driver', 'style'=>'display: inline-block; width: 220px;']);

					echo SelectWidget::widget([
						'model' => $trip_transport,
						'attribute' => 'driver_id',
						'name' => 'driver_id',
						'initValueText' => ($trip_transport->driver_id > 0 && $trip_transport->driver != null ? $trip_transport->driver->fio : ''),
						'options' => [
							'placeholder' => 'Введите название...',
							'class' => 'form-control_ change_driver' //change_driver - это класса для Славиной проверки в js
						],
						'ajax' => [
							'url' => '/trip-transport/ajax-get-drivers-names?trip_id='.$trip_transport->trip_id,
							'data' => new JsExpression('function(params, obj) {

								var drivers_ids = [];

								$("#car-info-form .trip-transport-row").each(function() {
									var driver_id = $(this).find(\'*[name="TripTransport[driver_id]"]\').val();
									if(driver_id.length > 0) {
										drivers_ids.push(driver_id);
									}
								});

								var selected_transport_id = $("#transport-info").attr("transport-id");

								return {
									search: params.search,
									selected_drivers_ids: drivers_ids,
									selected_transport_id: selected_transport_id
								};
							}'),
						],
						'using_delete_button' => false,
					]); ?>
				</div>
			</div>
			<div class="col-sm-3"><button type="button" class="change_driver_on_trip_transport btn btn-default" style="display: inline-block;">Сменить водителя</button></div>
		<?php }elseif(empty($trip_transport->driver)) { ?>
			<div class="col-sm-12"><b>Водитель:</b> <span style="color:red;">не определен</span></div>
		<?php }else { ?>
			<div class="col-sm-12"><b>Водитель:</b> <?= $trip_transport->driver->fio ?></div>
		<?php } ?>
	</div>

	<br /><br />
	<div class="row">
		<?php if(empty($trip_transport->date_sended)) { ?>
			<?php if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin', 'editor'])) { ?>
				<div class="col-sm-4 for_submit_button">
					<button class="btn btn-primary transport-confirmed-transport-info <?= ($trip_transport->confirmed == 1 ? 'disabled' : '') ?>" <?= ($trip_transport->confirmed == 1 ? 'disabled="true"' : '') ?> type="button" style="background-color:#5cb85c;">Подтвердить</button>
					<input type="hidden" name="confirmed_transport_info" value="<?= intval($trip_transport->confirmed) ?>">
				</div>
			<?php } ?>

			<?php if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin', 'editor'])) { ?>
				<div class="col-sm-4 for_submit_button" >
					<button class="btn btn-danger remove_from_trip" type="button">Снять с рейса</button>
				</div>
			<?php } ?>

		<?php } ?>

		<div class="col-sm-4 for_close_button">
			<button class="btn btn-default button-close" type="button" data-dismiss="modal">Закрыть</button>
		</div>
	</div>
</div>