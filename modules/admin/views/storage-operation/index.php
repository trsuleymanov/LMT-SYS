<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\StorageOperationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Storage Operations';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="storage-operation-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Storage Operation', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'storage_id',
            'storage_detail_id',
            'model_id',
            'count',
            //'transport_id',
            //'driver_id',
            //'created_at',
            //'operation_type_id',
            //'comment:ntext',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
