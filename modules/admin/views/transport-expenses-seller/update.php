<?php
$this->title = 'Редактирование продавца';
$this->params['breadcrumbs'][] = ['label' => 'Список', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Изменение';
?>
<div class="transport-expenses-seller-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
