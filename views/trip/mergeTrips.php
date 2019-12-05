<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\widgets\SelectWidget;
use yii\helpers\ArrayHelper;

$aNewTripTimes = $model->getTimesOfTrip($model->start_time, $model->mid_time, $model->end_time);

?>

<div class="mergeTrips">

	<?php /*
	<div class="controls form-inline merged_trip_list" style="width:100%; margin-left:0;">
	
		<label style="display:inline-block; text-align:right;">Объединяемые рейсы:</label>
		<span style="display:inline-block; width:10px;"></span>


		<?php foreach($trips as $key => $trip) { ?>
			<span style="font-weight: bold; text-decoration: underline; margin-right: 5px;"><?= $trip->name ?></span>
			<a class="merge-trip" href="#" <?= $key == 0 ? 'active="true"' : '' ?> start_time="<?= $trip->start_time ?>" mid_time="<?= $trip->mid_time ?>" end_time="<?= $trip->end_time ?>"  ><?= $trip->name ?></a>
		<?php } ?>
	</div>
	*/ ?>
    <?php $form = ActiveForm::begin([
		'id' => 'merge-trip-form'
	]); ?>

	<?php /*
	<?= $form->field($model, 'name')
		->label('Название рейса:')
		->input('name', ['style'=>'margin-left: 10px; height:30px; width:200px; display:inline-block;']);
 	*/
	?>

	<table class="time_points">
		<tr>
			<td>Объединяемые рейсы:</td>
			<td colspan="3" class="merged_trip_list">
				<?php foreach($trips as $key => $trip) { ?>
					<a class="merge-trip" href="#" <?= $key == 0 ? 'active="true"' : '' ?> start_time="<?= $trip->start_time ?>" mid_time="<?= $trip->mid_time ?>" end_time="<?= $trip->end_time ?>"  ><?= $trip->name ?></a>
				<?php } ?>
			</td>
		</tr>
		<tr>
			<td class="reis-name">Название рейса:</td>
			<td colspan="3" class="merged_trip_list">
				<?= $form->field($model, 'name', ['options' => ['class' => '']])
					->label(false)
					->input('name', ['style'=>'width: 235px;' ]);
				?>
			</td>
		</tr>

		<tr>
			<td>Интервал:</td>
			<td colspan="3">
				<?= $form->field($model, 'point_interval', ['options' => ['class' => '']])
					->label(false)
					->input('name', ['style'=>'width: 235px;' ]);
				?>
			</td>
		</tr>

		<tr>
			<td class="points-name">Точки:</td>
			<td valign="middle">
				<?= $form->field($model, 'start_time', ['options' => ['class' => '']])->input('start_time', ['class'=>'form-control input_start_time'])->label(false); ?>
			</td>
			<td valign="middle">
				<?= $form->field($model, 'mid_time', ['options' => ['class' => '']])->input('start_time', ['class'=>'form-control input_mid_time'])->label(false); ?>
			</td>
			<td valign="middle">
				<?= $form->field($model, 'end_time', ['options' => ['class' => '']])->input('end_time', ['class'=>'form-control input_end_time'])->label(false); ?>
			</td>
		</tr>

        <?php if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin'])) { ?>
            <tr>
                <td class="commercial-name">
                    Коммерческий рейс
                </td>
                <td colspan="3">
                    <?= $form->field($model, 'commercial')->checkbox(['label' => '', 'style' => 'width: 20px;' ]) ?>
                </td>
            </tr>
        <?php }else {

            echo Html::activeHiddenInput($model, 'commercial');
            //echo $form->field($model, 'commercial')->checkbox(['disabled' => true, 'label' => '', 'style' => 'width: 20px;',]);
        } ?>
	</table>

	<?php /*
	<div class="time_points row">
		<div class="col-sm-4 form-group text-right"><b>Точки:</b></div>
		<div class="point_start_time col-sm-2 form-group">
			<?= $form->field($model, 'start_time')->input('start_time', ['class'=>'form-control input_start_time'])->label(''); ?>
			<?= $form->field($model, 'start_time')->textInput(['maxlength' => true])->label(false) ?>
		</div>
		<div class="point_mid_time col-sm-2 form-group">
			<?= $form->field($model, 'mid_time')->label(false)->input('start_time', ['class'=>'form-control input_mid_time']); ?>
			<?= $form->field($model, 'mid_time')->textInput(['maxlength' => true])->label(false) ?>
		</div>
		<div class="point_end_time col-sm-2 form-group">
			<?= $form->field($model, 'end_time')->label(false)->input('end_time', ['class'=>'form-control input_end_time']); ?>
			<?= $form->field($model, 'end_time')->textInput(['maxlength' => true])->label(false) ?>
		</div>
		<div class="point_start_time col-sm-4 form-group">Точки:</div>
		<div class="point_mid_time col-sm-6 form-group">
			<?= $form->field($model, 'start_time')->input('start_time', ['class'=>'form-control input_start_time'])->label(''); ?>
			<?= $form->field($model, 'mid_time')->label(false)->input('start_time', ['class'=>'form-control input_mid_time']); ?>
			<?= $form->field($model, 'end_time')->label(false)->input('end_time', ['class'=>'form-control input_end_time']); ?>
		</div>
	</div>
 	*/ ?>

	<?php /*

	<div>
		<div>
			<?php if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin'])) { ?>
				<?= $form->field($model, 'commercial')->checkbox() ?>
			<?php }else { ?>
				<?= $form->field($model, 'commercial')->checkbox(['disabled' => true]) ?>
			<?php } ?>
		</div>
	</div>
 	*/ ?>

	<hr />
	<div class="row">
		<div class="col-sm-2 for_close_button">
			<button class="btn btn-default button-close" type="button" data-dismiss="modal">Закрыть</button>
		</div>
		<div class="col-sm-2 for_submit_button">
			<button class="btn btn-success button-submit" type="submit">Объединить рейсы</button>
		</div>
	</div>
        
    <?php ActiveForm::end(); ?>

</div>

