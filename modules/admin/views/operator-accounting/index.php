<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\date\DatePicker;
use app\models\DispatcherAccounting;
use app\models\User;
use app\widgets\periodPicker\PeriodPicker;
use kartik\export\ExportMenu;
use app\helpers\table\PageSizeHelper;
use yii\helpers\Url;
use yii\widgets\LinkPager;


$this->title = 'Учет работы оператора';
$this->params['breadcrumbs'][] = $this->title;

$pagination = $dataProvider->getPagination();
$pagination->totalCount = $dataProvider->getTotalCount();

$columns = [
    [
        'attribute' => 'operation_type',
        'content' => function($model) {
            if(isset(DispatcherAccounting::getOperationTypes()[$model->operation_type])) {
                return DispatcherAccounting::getOperationTypes()[$model->operation_type];
            }else {
                return $model->operation_type;
            }
        },
        'filter' => Html::activeDropDownList(
            $searchModel,
            'operation_type',
            ['' => 'Все'] + DispatcherAccounting::getOperationTypes(),
            ['class' => "form-control"]
        )
    ],
    [
        'attribute' => 'user_fio',
        'label' => 'Пользователь',
        'content' => function($model) {
            $user = User::findOne($model->dispetcher_id);
            if($user == null) {
                return $model->dispetcher_id;
            }else {
                return $user->firstname.' '.$user->lastname;
            }
        },

    ],
    [
        'attribute' => 'created_at',
        'content' => function($model) {
            return date('d.m.Y H:i', $model->created_at);
        },
        'filter' => PeriodPicker::widget([
            'model' => $searchModel,
            'attribute' => 'created_at',
            'isFilterInGridView' => true,
            'clearButton' => false
        ])
    ],
//    [
//        'attribute' => 'call_appeal_id',
//        'content' => function($model) {
//            return (!empty($model->call_appeal_id) ? Html::a($model->call_appeal_id,  Url::to(['/admin/call-appeal/index?CallAppealSearch[id]='.$model->call_appeal_id])) : '');
//        },
//    ],
    [
        'attribute' => 'order_id',
        'content' => function($model) {
            if($model->order_id > 0) { // /admin/order?OrderSearch[id]=1357
                return Html::a($model->order_id, ['/admin/order', 'OrderSearch[id]' => $model->order_id], ['target'=>'_blank']);
            }else {
                return '';
            }
        }
    ],
    [
        'attribute' => 'value',
        'content' => function($model) {
            if($model->operation_type == 'cancel_trip_sended') {
                $trip = \app\models\Trip::find()->where(['id' => $model->value])->one();
                return ($trip == null ? $model->value : date('d.m.Y', $trip->date).' '.$trip->direction->sh_name.' '.$trip->name.' ['.$model->value.']');
            }else {
                return (!empty($model->value) ? $model->value : '');
            }
        }
    ],

];

?>
<div id="dispatcher-accounting-index" class="box box-default" >


    <div class="box-header scroller with-border">

        <div class="pull-left">
            <?php
            $gridColumns = [
                ['class' => 'kartik\grid\SerialColumn'],
            ];
            $gridColumns = array_merge($gridColumns, $columns);

            echo '<div style="vertical-align: top; display: inline-block;">'.ExportMenu::widget([
                    'dataProvider' => $dataProvider,
                    'columns' => $gridColumns,
                    'fontAwesome' => true
                ]).'</div>';
            ?>
        </div>

        <div class="pull-left">
            <?= LinkPager::widget([
                'pagination' => $pagination,
                'options' => ['class' => 'pagination pagination-sm']
            ]); ?>
        </div>
        <?= (new PageSizeHelper([20, 50, 100, 200, 500]))->getButtons() ?>
    </div>

    <div></div>

    <div class="box-body box-table">
        <?php
        $gridColumns = [
            ['class' => 'yii\grid\SerialColumn'],
        ];
        $gridColumns = array_merge($gridColumns, $columns);

        echo GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => $gridColumns,
        ]); ?>
    </div>
</div>