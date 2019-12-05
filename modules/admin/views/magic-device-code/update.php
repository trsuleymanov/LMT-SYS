<?php
$this->title = 'Добавление магического устройства';
$this->params['breadcrumbs'][] = ['label' => 'Магические устройства', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Изменение';
?>
<div class="magic-device-code-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
