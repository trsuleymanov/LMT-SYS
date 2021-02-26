<?php

$this->title = 'Создание способа оплаты';
$this->params['breadcrumbs'][] = ['label' => 'Способы оплаты', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="transport-payment-methods-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
