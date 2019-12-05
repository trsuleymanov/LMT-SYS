<?php

$this->title = 'Создание типа расхода';
$this->params['breadcrumbs'][] = ['label' => 'Список типов расходов', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="transport-expenses-types-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
