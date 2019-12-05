<?php

$this->title = 'Создание вида документа';
$this->params['breadcrumbs'][] = ['label' => 'Список видов', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="transport-expenses-doc-type-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
