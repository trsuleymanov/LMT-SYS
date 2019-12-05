<?php
use yii\helpers\Html;


$this->title = 'Создание детали';
$this->params['breadcrumbs'][] = ['label' => 'Номенклатура', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="nomenclature-detail-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
