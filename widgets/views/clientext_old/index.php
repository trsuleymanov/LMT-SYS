<?php
use app\components\Helper;
use app\assets\ClientextWidgetAsset;

ClientextWidgetAsset::register($this);

if($clientexts_orders_count > 0) {
    ?><div class="clientext-widget">
        <div class="number"><?= $clientexts_orders_count ?></div>
        <div class="title"><?= Helper::getNumberString($clientexts_orders_count, 'З<br />А<br />Я<br />В<br />К<br />А', 'З<br />А<br />Я<br />В<br />К<br />И', 'З<br />А<br />Я<br />В<br />О<br />К')?></div>
    </div>
<?php } ?>