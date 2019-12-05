<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Direction;
use yii\helpers\ArrayHelper;

?>
<div class="Trip">

    <?php $form = ActiveForm::begin(); ?>

		<?php
			if($model->isNewRecord){ ?>
				<div class="row">
					<div class="col-sm-4 form-group no-padding" align="right" style="margin-top: 7px;">
						<b>Направление:</b>
					</div>
					<div class="col-sm-4 form-group no-padding">
						<?= $form->field($model, 'direction_id')->label(false)->dropDownList(ArrayHelper::map(Direction::find()->all(), 'id', 'sh_name'), ['prompt' => 'Выберите направление']); ?>
					</div>
				</div>
				<?php
			}
		?>

		<div class="row">
			<div class="col-sm-4 form-group no-padding" align="right" style="margin-top: 7px;">
				<b>Название рейса:</b>
			</div>

			<?php if(empty($model->date_sended)) { ?>
				<div class="col-sm-4 form-group no-padding">
					<?= $form->field($model, 'name')->label(false)->input('name'); ?>
				</div>
			<?php }else { ?>
				<div class="col-sm-4 form-group no-padding" style="margin-top: 7px; margin-left: 10px;">
					<?= $model->name ?>
				</div>
			<?php } ?>
		</div>

		<div class="row">
			<div class="col-sm-4 form-group no-padding" align="right" style="margin-top: 7px;">
				<b>Интервал:</b>
			</div>
			<div class="col-sm-8 form-group no-padding">
				<?= $form->field($model, 'point_interval', ['options' => ['style' => 'display: inline-block; width: 67px;']])->label(false); ?>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-4 form-group no-padding" align="right" style="margin-top: 7px;">
				<b>Точки:</b>
			</div>

			<?php if(empty($model->date_start_sending)) { ?>
				<div class="col-sm-8 form-group no-padding">
					<?= $form->field($model, 'start_time', ['options' => ['style' => 'display: inline-block; width: 67px;']])->label(false); ?>
					<?= $form->field($model, 'mid_time', ['options' => ['style' => 'display: inline-block; width: 67px;']])->label(false); ?>
					<?= $form->field($model, 'end_time', ['options' => ['style' => 'display: inline-block; width: 67px;']])->label(false); ?>
				</div>
			<?php }else  { ?>
				<div class="col-sm-8 form-group no-padding" style="margin-top: 7px; margin-left: 10px;">
					<?= $model->start_time ?>
					&nbsp;&nbsp;&nbsp;<?= $model->mid_time ?>
					&nbsp;&nbsp;&nbsp;<?= $model->end_time ?>
				</div>
			<?php } ?>
		</div>

		<div class="row">
			<div class="col-sm-4 form-group no-padding" align="right" style="margin-top: 7px;">
				<b>Коммерческий рейс</b>
			</div>
			<div class="col-sm-8 form-group no-padding">
				<?php if(!empty($model->date_start_sending)) { ?>
					<div style="margin-top: 7px; margin-left: 10px;"><?= ($model->commercial == 1 ? 'да' : 'нет') ?></div>
				<?php }elseif(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin'])) { ?>
					<?= Html::activeCheckbox($model, 'commercial', ['label' => false]); ?>
				<?php }else { ?>
					<?php if(!$model->isNewRecord){ ?>
						<div style="margin-top: 7px; margin-left: 10px;"><?= ($model->commercial == 1 ? 'да' : 'нет') ?></div>
					<?php } ?>
				<?php } ?>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-2 for_close_button">
				<button class="btn btn-default button-close" type="button" data-dismiss="modal">Закрыть</button>
			</div>
			<?php if(empty($model->date_sended)) { ?>
				<div class="col-sm-2 for_submit_button">
					<button class="btn btn-primary button-submit" type="submit">Записать данные рейса</button>
				</div>
			<?php } ?>
		</div>


        <div class="form-group"></div>
    <?php ActiveForm::end(); ?>

</div>
