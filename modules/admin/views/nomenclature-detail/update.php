<?php

$this->title = 'Редактирование детали';
$this->params['breadcrumbs'][] = ['label' => 'Номенклатура', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Изменение';
?>
<div class="nomenclature-detail-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]); ?>
</div>
