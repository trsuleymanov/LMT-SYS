<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\TransportWaybillType */

$this->title = 'Редактирование типа';
$this->params['breadcrumbs'][] = ['label' => 'Список типов', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Изменение';
?>
<div class="transport-waybill-type-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
