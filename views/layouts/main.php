<?php

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use kartik\date\DatePicker;
use app\components\Helper;
use yii\bootstrap\Modal;

AppAsset::register($this);

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
if($current_route == 'site/index') { ?>

	<div class="wrap <?= Helper::getMainClass(Yii::$app->request->get('date')) ?>">
		<?= $this->render('top-menu'); ?>
		<?= Breadcrumbs::widget([
			'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
		]) ?>

		<div id="page-area">
			<div id="notification-area">
				&nbsp;
			</div><div id="content-area">
				<?= $content ?>
			</div>
		</div>
	</div>

<?php }elseif($current_route == 'trip/trip-orders') // страница "Состав рейса"
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
		<div class="container" style="padding-left: 0; padding-right: 0; padding-top: 10px;">
			<?= $content ?>
		</div>
	</div>

	<?php /*
	<div class="wrap <?= Helper::getMainClass(Yii::$app->request->get('date')) ?>">
		<?= $this->render('top-menu-trip-orders', [
			'date' => $date,
			'trip' => $trip,
		]); ?>
		<div id="page-area">
			<div id="notification-area">
				&nbsp;
			</div><div id="content-area">
				<?= $content ?>
			</div>
		</div>
	</div>*/ ?>

<?php }elseif($current_route == 'trip/set-trips') { // страница "Расстановка" ?>
	<div class="wrap <?= Helper::getMainClass(Yii::$app->request->get('date')) ?>">
		<?= $this->render('top-menu-settrips'); ?>
		<?php /*
		<div class="container" style="padding-left: 0; padding-right: 0; padding-top: 20px;">
			<?= Breadcrumbs::widget([
				'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
			]) ?>
			<?= $content ?>
		</div>*/ ?>
		<div id="page-area">
			<div id="notification-area">
				&nbsp;
			</div><div id="content-area">
				<?= Breadcrumbs::widget([
					'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
				]) ?>
				<?= $content ?>
			</div>
		</div>
	</div>

<?php }elseif($current_route == 'call/get-call-window') { ?>
	<?= $content ?>
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
	<?php /*
	<div class="wrap <?= Helper::getMainClass(Yii::$app->request->get('date')) ?>">
		<?= $this->render('top-menu'); ?>
		<?= Breadcrumbs::widget([
			'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
		]) ?>

		<div id="page-area">
			<div id="notification-area">
				&nbsp;
			</div><div id="content-area">
				<?= $content ?>
			</div>
		</div>
	</div>
 	*/ ?>
<?php } ?>

<?php /*
<div id="clientext-block">
	<?php
	if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin'])) {
		echo app\widgets\ClientextWidget::widget();
	}
	?>
</div>
*/ ?>

<div id="incoming-orders-block">
	<?php
	if($current_route != 'call/get-call-window' && in_array(Yii::$app->session->get('role_alias'), ['root', 'admin', 'editor', 'manager'])) {
		echo app\widgets\IncomingOrdersWidget::widget();
	}
	?>
</div>


<div id="chat-block">
	<?= app\widgets\ChatWidget::widget([
		'is_open' => false
	]); ?>
</div>

<?php /*
<div id="msg-from-driver" style="display: none;">
	<div class="modal-title"><span>Сообщение от водителя</span>&nbsp;<button type="button" class="modal-close">×</button></div>
	<div class="modal-body"></div>
</div>
*/ ?>

<?php $this->endBody() ?>

<?= $this->render('modals') ?>

</body>
</html>
<?php $this->endPage() ?>
