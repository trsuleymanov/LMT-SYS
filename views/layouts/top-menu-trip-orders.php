<?php
use app\components\Helper;
?>

<div id="top-menu">
    <div class="container" style="padding-left: 0; padding-right: 0;">
        <div class="row">
            <div class="page_name" style="display:inline-block; float:left; text-align:center; font-weight:bold; font-size:large; width:25%;">
                <div style="margin-top:10px;">Состав рейса</div>
            </div>

            <div class="col-tobus-center1">
                <p id="selected-day" date="<?= $date ?>" align="center">
                    <?= $trip->direction->sh_name ?> <?= $trip->name ?>, <?= Helper::getMainDate($trip->date, 2); ?>
                </p>
            </div>

            <div class="today-item col-tobus-right-1"></div>
            <div class="col-tobus-right-4">
                <a href="/?date=<?= date('d.m.Y', $date) ?>">LMT-SYS</a>
            </div>
            <div class="col-tobus-right-5">
                Текущая дата:<br />
                <span id="system-time"><?= Helper::getMainDate(time(), 1); ?></span>
            </div>

        </div>
    </div>
</div>