<?php
use app\components\Helper;

$date = empty(Yii::$app->request->get('date')) ? date('d.m.Y') : Yii::$app->request->get('date');
$day_code = Helper::getDayCode($date);

$current_module = Yii::$app->controller->module->id;
$current_controller = Yii::$app->controller->id;
$current_route = $this->context->route;
//echo "current_route=$current_route <br />";
?>

<div id="top-menu">
    <div class="container" style="padding-left: 0; padding-right: 0;">
        <div class="row">
            <div class="col-tobus-center1">
                <p id="selected-day" date="<?= (!empty(Yii::$app->request->get('date')) ? Yii::$app->request->get('date') : date('d.m.Y')) ?>" align="center"><?= Helper::getMainDate((!empty(Yii::$app->request->get('date')) ? strtotime(Yii::$app->request->get('date')) : time()), 2); ?></p>
            </div>
            <div class="today-item col-tobus-right-1 <?= $current_module == 'waybill' ? 'active' : '' ?>">
                <a id="goto-today" href="/waybill/transport-waybill/list">Учет</a>
            </div>

            <div class="col-tobus-right-4">
                <?php /*<a href="/storage">ß ТОБУС</a> */ ?>
                <a href="/waybill/transport-waybill/list">LMT-SYS</a>
            </div>
            <div class="col-tobus-right-5">
                Текущая дата:<br />
                <span id="system-time"><?= Helper::getMainDate(time(), 1); ?></span>
            </div>
	   
        </div>
    </div>
</div>
