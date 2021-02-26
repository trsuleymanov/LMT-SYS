<?php

$this->title = 'Редактирование события';
$this->params['breadcrumbs'][] = ['label' => 'События', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Изменение';
?>
<div class="transport-waybill-trip-events-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
