<?php
$this->title = 'Редактирование способа оплаты';
$this->params['breadcrumbs'][] = ['label' => 'Способы оплаты', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Изменение';
?>
<div class="transport-payment-methods-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
