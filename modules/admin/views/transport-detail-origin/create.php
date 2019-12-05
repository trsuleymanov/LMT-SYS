<?php

use yii\helpers\Html;


$this->title = 'Создание происхождения';
$this->params['breadcrumbs'][] = ['label' => 'Номенклатура', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="transport-detail-origin-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
