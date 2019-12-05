<?php
use app\components\Helper;

$date = empty(Yii::$app->request->get('date')) ? date('d.m.Y') : Yii::$app->request->get('date');
$day_code = Helper::getDayCode($date);
?>

<div id="top-menu">
    <div class="container" style="padding-left: 0; padding-right: 0;">
        <div class="row">
            <div class="col-tobus-center1">
                <p id="selected-day" date="<?= (!empty(Yii::$app->request->get('date')) ? Yii::$app->request->get('date') : date('d.m.Y')) ?>" align="center"><?= Helper::getMainDate((!empty(Yii::$app->request->get('date')) ? strtotime(Yii::$app->request->get('date')) : time()), 2); ?></p>
            </div>
            <div class="today-item col-tobus-right-1 <?= $day_code == 'today' ? 'active' : '' ?>">
                <a id="goto-today" href="/?date=<?= date('d.m.Y') ?>">Сегодня</a>
            </div>
            <div class="tomorrow-item col-tobus-right-2 <?= $day_code == 'tomorrow' ? 'active' : '' ?>">
                <a id="goto-tomorrow" href="/?date=<?= date('d.m.Y', time() + 86400) ?>">Завтра</a>
            </div>
            <div class="another-day-item col-tobus-right-3 <?= $day_code == 'another-day' ? 'active' : '' ?>">
                <input id="another-day" name="another-day" type="text" value=""  />
                <label id="goto-another-day" for="another-day">Другой день</label>
            </div>
            <div class="col-tobus-right-4">
                <?php /*<a href="/">ß ТОБУС</a> */ ?>
                <a href="/">LMT-SYS</a>
            </div>
            <div class="col-tobus-right-5">
                Текущая дата:<br />
                <span id="system-time"><?= Helper::getMainDate(time(), 1); ?></span>
            </div>
	   
        </div>
    </div>
</div>
