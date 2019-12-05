<?php
use yii\helpers\Html;
use app\components\Helper;
use app\models\Direction;
use app\models\Trip;
use app\models\ScheduleTrip;
use yii\helpers\Url;
use yii\widgets\MaskedInput;

$this->registerJsFile('js/site/zerostate.js', ['depends'=>'app\assets\AppAsset']);
?>
<p>Очистка таблицы заказов (order), а также обнуление в таблице клиентов: кол-ва заказов, призовых поездок и штрафов</p>
<?= Html::a('Очистить', '#', ['id' => 'clear-order-data', 'class' => 'btn btn-danger']); ?>
<hr />

<p>Очистка таблицы рейсов (trip), а также очистка полей связанных данных: `order`.trip_id и `trip_transport`.trip_id</p>
<?= Html::a('Очистить', '#', ['id' => 'clear-trip-data', 'class' => 'btn btn-danger']); ?>
<hr />

<p>Очистка таблицы машин на рейсе (trip_transport), а также очистка полей связанных данных: `order`.fact_trip_transport_id, `order`.confirm_selected_transport, `order`.time_sat</p>
<?= Html::a('Очистить', '#', ['id' => 'clear-trip-transport-data', 'class' => 'btn btn-danger']); ?>
<hr />

<p>Очистка таблицы отчета отображаемого дня (day_report_trip_transport)</p>
<?= Html::a('Очистить', '#', ['id' => 'clear-day-report-trip-transport-data', 'class' => 'btn btn-danger']); ?>
<hr />

<p>Очистка таблицы клиентов (client), а также полей связанных данных: `order`.client_id</p>
<?= Html::a('Очистить', '#', ['id' => 'clear-client-data', 'class' => 'btn btn-danger']); ?>
<hr />
