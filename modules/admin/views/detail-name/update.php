<?php

$this->title = 'Редактирование названия';
$this->params['breadcrumbs'][] = ['label' => 'Наименования деталей', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Изменение';
?>
<div class="detail-name-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
