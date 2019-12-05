<?php

use yii\helpers\Html;


$this->title = 'Создание события';
$this->params['breadcrumbs'][] = ['label' => 'События', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="transport-waybill-trip-events-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
