<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\TransportExpenses */

$this->title = 'Create Transport Expenses';
$this->params['breadcrumbs'][] = ['label' => 'Transport Expenses', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="transport-expenses-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
