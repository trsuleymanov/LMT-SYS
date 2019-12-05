<?php
use yii\helpers\Html;

$this->title = 'Редактирование кэш-бэка ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Список кэш-бэков', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Изменение';
?>
<div class="cashback-setting-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
