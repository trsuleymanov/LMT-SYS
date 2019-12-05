<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\CallCase */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Call Cases', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="call-case-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'case_type',
            'order_id',
            'open_time:datetime',
            'call_count',
            'status',
            'close_time:datetime',
        ],
    ]) ?>

</div>
