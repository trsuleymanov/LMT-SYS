<?php

use yii\helpers\Html;


$this->title = 'Создание состояния запчасти';
$this->params['breadcrumbs'][] = ['label' => 'Состояния запчастей', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="transport-detail-state-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
