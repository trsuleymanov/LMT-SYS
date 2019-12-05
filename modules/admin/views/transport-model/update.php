<?php
use yii\helpers\Html;

$this->title = 'Редактирование модели';
$this->params['breadcrumbs'][] = ['label' => 'Модели', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Изменение';
?>
<div class="transport-model-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
