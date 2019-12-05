<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\TransportExpensesDetailing */

$this->title = 'Update Transport Expenses Detailing: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Transport Expenses Detailings', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="transport-expenses-detailing-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
