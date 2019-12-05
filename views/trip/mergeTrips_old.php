<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\widgets\SelectWidget;
use yii\helpers\ArrayHelper;

$aNewTripTimes = $model->getTimesOfTrip($model->start_time, $model->mid_time, $model->end_time);

?>

<div class="mergeTrips">

	<div class="controls form-inline merged_trip_list" style="width:100%; margin-left:0;">
	
		<label style="display:inline-block; text-align:right;">Объединяемые рейсы:</label>
		<span style="display:inline-block; width:10px;"></span>


		<?php foreach($trips as $key => $trip) { ?>
			<?php /*<span style="font-weight: bold; text-decoration: underline; margin-right: 5px;"><?= $trip->name ?></span> */ ?>
			<a class="merge-trip" href="#" <?= $key == 0 ? 'active="true"' : '' ?> start_time="<?= $trip->start_time ?>" mid_time="<?= $trip->mid_time ?>" end_time="<?= $trip->end_time ?>"  ><?= $trip->name ?></a>
		<?php } ?>
	</div>

    <?php $form = ActiveForm::begin([
		'id' => 'merge-trip-form'
	]); ?>
	
	<?= $form->field($model, 'name')
		->label('Название рейса:')
		->input('name', ['style'=>'margin-left: 10px; height:30px; width:200px; display:inline-block;']);
	?>
	
	
	<div class="time_points">
		<div class="point_start_time">
			<?= $form->field($model, 'start_time')->input('start_time', ['style'=>'display:inline-block; width:70px; margin-left: 10px;', 'class'=>'form-control input_start_time'])
			->label('Точки:'); ?>
		</div>
		<div class="point_mid_time" style="width:70px;">
			<?= $form->field($model, 'mid_time')->label(false)->input('start_time', ['class'=>'form-control input_mid_time']); ?>
		</div>
		<div class="point_end_time" style="width:70px;">
			<?= $form->field($model, 'end_time')->label(false)->input('end_time', ['class'=>'form-control input_end_time']); ?>
		</div>
	</div>

	<div>
		<div>
			<?php if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin'])) { ?>
				<?= $form->field($model, 'commercial')->checkbox() ?>
			<?php }else { ?>
				<?= $form->field($model, 'commercial')->checkbox(['disabled' => true]) ?>
			<?php } ?>
		</div>
	</div>
        
    <?php ActiveForm::end(); ?>
</div>

