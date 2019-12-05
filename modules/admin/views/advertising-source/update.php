<?php

$this->title = 'Редактирование источника &laquo;' . $model->name . '&raquo;';
$this->params['breadcrumbs'][] = ['label' => 'Источники', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Изменение';
?>
<div class="advertising-source-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
