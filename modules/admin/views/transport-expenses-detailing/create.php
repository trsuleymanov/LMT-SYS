<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\TransportExpensesDetailing */

$this->title = 'Create Transport Expenses Detailing';
$this->params['breadcrumbs'][] = ['label' => 'Transport Expenses Detailings', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="transport-expenses-detailing-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
