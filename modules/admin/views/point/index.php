<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PointSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Points';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="point-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Point', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            'city_id',
            'alias',
            // 'point_of_arrival',
            // 'critical_point',
            // 'created_at',
            // 'updated_at',
            // 'active',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
                'options' => ['style' => 'width: 50px;']
            ],
        ],
    ]); ?>
</div>
