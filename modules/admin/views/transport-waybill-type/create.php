<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\TransportWaybillType */

$this->title = 'Создание типа ПЛ';
$this->params['breadcrumbs'][] = ['label' => 'Список типов', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="transport-waybill-type-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
