<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\DriverPhoto */

$this->title = 'Update Driver Photo: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Driver Photos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="driver-photo-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
