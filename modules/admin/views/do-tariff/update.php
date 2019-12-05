<?php
$this->title = 'Редактирование признака ' . $model->description;
$this->params['breadcrumbs'][] = ['label' => 'Список признаков', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Изменение';
?>
<div class="do-tariff-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
