<?php
use app\components\Helper;

$date = empty(Yii::$app->request->get('date')) ? date('d.m.Y') : Yii::$app->request->get('date');
$day_code = Helper::getDayCode($date);
?>

<div id="top-menu">
    <div class="container" style="padding-left: 0; padding-right: 0;">
        <div class="row">
	    <div class="page_name" style="display:inline-block; float:left; text-align:center;font-weight:bold;font-size:large;width:25%;">
		<div style="margin-top:10px;">Расстановка</div>
	    </div>
	
            <div class="col-tobus-center1">
                <p id="selected-day" date="<?= (!empty(Yii::$app->request->get('date')) ? Yii::$app->request->get('date') : date('d.m.Y')) ?>" align="center"><?= Helper::getMainDate((!empty(Yii::$app->request->get('date')) ? strtotime(Yii::$app->request->get('date')) : time()), 2); ?></p>
            </div>
	    
            <div class="today-item col-tobus-right-1 <?= $day_code == 'today' ? 'active' : '' ?>"></div>
            <div class="col-tobus-right-4">
                <a href="<?= (!empty(Yii::$app->request->get('date')) ? '/?date='.Yii::$app->request->get('date') : '/') ?>">LMT-SYS</a>
            </div>
            <div class="col-tobus-right-5">
                Текущая дата:<br />
                <span id="system-time"><?= Helper::getMainDate(time(), 1); ?></span>
            </div>
	   
        </div>
    </div>
</div>
