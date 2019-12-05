<?php

use yii\helpers\Html;

$this->title = 'Редактирование состояние запчасти';
$this->params['breadcrumbs'][] = ['label' => 'Состояния запчастей', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Изменение';
?>
<div class="transport-detail-state-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
