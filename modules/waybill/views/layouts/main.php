<?php

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\StorageAsset;
use kartik\date\DatePicker;
use app\components\Helper;
use yii\bootstrap\Modal;

StorageAsset::register($this);

$current_route = $this->context->route;
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<?php
/*
if($current_route == 'trip/trip-orders') // страница "Состав рейса"
{
	$trip_id = Yii::$app->request->get('trip_id');
	$trip = \app\models\Trip::findOne($trip_id);
	if($trip == null) {
		throw new \yii\web\ForbiddenHttpException('Рейс не найден');
	}

	$date = $trip->date;
	$day_code = Helper::getDayCode($date);
	?>
	<div class="wrap <?= Helper::getMainClass(date('d.m.Y', $date)) ?>">
		<?= $this->render('top-menu-trip-orders', [
			'date' => $date,
			'trip' => $trip,
		]); ?>
		<div class="container" style="padding-left: 0; padding-right: 0; padding-top: 80px;">
			<?= $content ?>
		</div>
	</div>
<?php }elseif($current_route == 'trip/set-trips') { // страница "Расстановка" ?>
	<div class="wrap <?= Helper::getMainClass(Yii::$app->request->get('date')) ?>">
		<?= $this->render('top-menu-settrips'); ?>
		<div class="container" style="padding-left: 0; padding-right: 0; padding-top: 20px;">
			<?= Breadcrumbs::widget([
				'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
			]) ?>
			<?= $content ?>
		</div>
	</div>
<?php }else { // Все остальные страницы ?>
	<div class="wrap <?= Helper::getMainClass(Yii::$app->request->get('date')) ?>">
		<?= $this->render('top-menu'); ?>
		<div class="container" style="padding-left: 0; padding-right: 0; padding-top: 20px;">
			<?= Breadcrumbs::widget([
				'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
			]) ?>
			<?= $content ?>
		</div>
	</div>
<?php }*/ ?>

<div class="wrap <?= Helper::getMainClass(Yii::$app->request->get('date')) ?>">
	<?= $this->render('top-menu'); ?>
	<div class="container" style="padding-left: 0; padding-right: 0; padding-top: 20px;">
		<?= Breadcrumbs::widget([
			'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
		]) ?>
		<?= $content ?>
	</div>
</div>

<?php $this->endBody() ?>

<div id="default-modal" class="fade modal" role="dialog" tabindex="-1">
	<div class="modal-dialog modal-md" style="width: 800px;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<span class="modal-title">Заголовок...</span>
			</div>
			<div class="modal-body">
				<div id="modal-content">Загружаю...</div>
			</div>
		</div>
	</div>
</div>

<div id="move-expense-to-another-pl-modal" class="fade modal" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-md" style="width: 480px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <span class="modal-title">Перенос расхода в другой путевой лист</span>
            </div>
            <div class="modal-body">
                <div id="modal-content">Загружаю...</div>
            </div>
        </div>
    </div>
</div>

<?php /*
<div id="order-create-modal" class="fade modal" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-md" style="width: 600px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <span class="modal-title">Запись заказа</span>
            </div>
            <div class="modal-body">
                <div id="modal-content">Загружаю...</div>

            </div>
        </div>
    </div>
</div>

<div id="clientext-modal" class="fade modal" style="width: 0; height: 0; z-index: 1045;" role="dialog" tabindex="-1">
	<div class="modal-dialog modal-md" style="width: 500px;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<span class="modal-title">Свежие заявки</span>
			</div>
			<div class="modal-body">
				<div id="modal-content">Загружаю...</div>
			</div>
		</div>
	</div>
</div>


<div id="client-last-orders-modal" class="fade modal" role="dialog" tabindex="-1">
	<div class="modal-dialog modal-md" style="width: 520px;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<span class="modal-title">Возможны дубликаты</span>
			</div>
			<div class="modal-body">
				<div id="modal-content">Загружаю...</div>
			</div>
		</div>
	</div>
</div>

<div id="clientext-block">
	<?= app\widgets\ClientextWidget::widget(); ?>
</div>
*/ ?>
</body>
</html>
<?php $this->endPage() ?>
