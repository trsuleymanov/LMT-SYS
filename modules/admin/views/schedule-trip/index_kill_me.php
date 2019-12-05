<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ScheduleTripSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Trip Statics';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="schedule-trip-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Trip Static', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            'schedule_id',
            'start_time',
            'mid_time',
            'end_time',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
                'options' => ['style' => 'width: 50px;']
            ],
        ],
    ]); ?>
</div>
