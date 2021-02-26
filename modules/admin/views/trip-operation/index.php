<?php

use app\models\TripOperation;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use app\models\User;
use app\widgets\periodPicker\PeriodPicker;


$this->title = 'Действия с рейсами';
$this->params['breadcrumbs'][] = $this->title;
?>
<div id="trip-operation--page" class="box box-default" >
    <div class="box-header scroller with-border">
        <div class="pull-left"></div>
    </div>
    <div></div>

    <div class="box-body box-table">
        <?php

        echo GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                //'id',
                [
                    'attribute' => 'created_at',
                    'content' => function($model) {
                        return $model->created_at > 0 ? date('d.m.Y H:i', $model->created_at) : '';
                    },
                    'filter' => PeriodPicker::widget([
                        'model' => $searchModel,
                        'attribute' => 'created_at',
                        'isFilterInGridView' => true,
                        'clearButton' => false
                    ])
                ],
                [
                    'attribute' => 'user_id',
                    'content' => function($model) {
                        return ($model->user != null ? $model->user->fullname : '');
                    },
                    'filter' => Html::activeDropDownList(
                        $searchModel,
                        'user_id',
                        ['' => 'Все'] + ArrayHelper::map(User::find()->where(['!=', 'role_id', 5])->all(), 'id', 'fullname'),
                        ['class' => "form-control"]
                    )
                ],

                [
                    'attribute' => 'type',
                    'content' => function($model) {
                        return TripOperation::getOperations()[$model->type];
                    },
                    'filter' => Html::activeDropDownList(
                        $searchModel,
                        'type',
                        ['' => 'Все'] + TripOperation::getOperations(),
                        ['class' => "form-control"]
                    )
                ],
                [
                    'attribute' => 'comment',
                    'content' => function($model) {
                        return (!empty($model->comment) ? $model->comment : '');
                    }
                ],
                [
                    'attribute' => 'delta',
                    'content' => function($model) {
                        return (!empty($model->delta) ? $model->delta : '');
                    }
                ],

                //['class' => 'yii\grid\ActionColumn'],
            ],
        ]); ?>
    </div>
</div>
