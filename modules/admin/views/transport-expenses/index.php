<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\TransportExpensesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Transport Expenses';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="transport-expenses-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Transport Expenses', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'transport_waybill_id',
            'doc_number',
            'expenses_doc_type_id',
            'expenses_type_id',
            //'expenses_seller_type_id',
            //'price',
            //'check_attached',
            //'expenses_seller_id',
            //'count',
            //'points',
            //'expenses_is_taken',
            //'expenses_is_taken_comment',
            //'payment_method_id',
            //'need_pay_date',
            //'payment_date',
            //'transport_expenses_paymenter_id',
            //'payment_comment',
            //'created_at',
            //'creator_id',
            //'updated_at',
            //'updator_id',
            //'view_group',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
