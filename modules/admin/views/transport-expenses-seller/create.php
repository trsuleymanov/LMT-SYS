<?php
$this->title = 'Создание продавца';
$this->params['breadcrumbs'][] = ['label' => 'Список', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="transport-expenses-seller-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
