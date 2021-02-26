<?php
$this->title = 'Редактирование вида документа';
$this->params['breadcrumbs'][] = ['label' => 'Список видов', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Изменение';
?>
<div class="transport-expenses-doc-type-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
