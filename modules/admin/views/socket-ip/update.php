<?php
$this->title = 'Редактирование ip-адреса';
$this->params['breadcrumbs'][] = ['label' => 'Список адресов', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Изменение';
?>
<div class="socket-ip-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
