<?php
$this->title = 'Редактирование типа расхода';
$this->params['breadcrumbs'][] = ['label' => 'Список типов расходов', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Изменение';
?>
<div class="transport-expenses-types-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
