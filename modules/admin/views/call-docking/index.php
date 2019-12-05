<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CallDockingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Call Dockings';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="call-docking-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Call Docking', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'call_id',
            'case_id',
            'conformity',
            'click_event',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
