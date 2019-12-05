<?php
/*
 * Заказы на рейсе + данные рейса и выбранной машины
 */

\app\assets\AdminAsset::register($this);  // пока из админского ассета позаимстуем стили для таблицы

$this->registerJsFile('js/site/create-order-modal.js', ['depends'=>'app\assets\AppAsset']);
$this->registerJsFile('https://api-maps.yandex.ru/2.1/?lang=ru_RU&apikey=5c7acdc8-48c9-43d9-9f44-2e6b9e178101', ['depends' => 'app\assets\AdminAsset']);

?>
<div id="trip-orders-page" trip-id="<?= $trip->id ?>">
    <?= $this->render('_ajax-trip-orders-block', [
        'trip' => $trip,
        'orderSearchModel' => $orderSearchModel,
        'orderDataProvider' => $orderDataProvider,
        'transportSearchModel' => $transportSearchModel,
        'transportDataProvider' => $transportDataProvider,
        'open_popup_order_id' => null,
        'open_popup_field' => null
    ]) ?>
</div>