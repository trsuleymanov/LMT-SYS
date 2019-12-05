<?php
use yii\grid\GridView;
use yii\helpers\Url;
use yii\helpers\Html;
?>

<?php

$columns = [

    ['class' => 'yii\grid\SerialColumn'],
    //'id',
    'name',
//                [
//                    'attribute' => 'schedule_id',
//                    'content' => function($model) {
//                        return $model->schedule->name;
//                    }
//                ],
    'start_time',
    'mid_time',
    'end_time',
];

if($edit_delete_trips == true) {
    $columns[] = [
        'class' => 'yii\grid\ActionColumn',
        'template' => '{update} {delete}',
        'options' => ['style' => 'width: 50px;'],
        'buttons' => [
            'update' => function ($url, $model) {
                return Html::a(
                    '<span class="glyphicon glyphicon-pencil"></span>',
                    Url::to(['/admin/schedule-trip/update', 'id' => $model->id]),
                    ['aria-label' => 'Редактирование']
                );
            },
            'delete' => function ($url, $model) {
                return Html::a(
                    '<span class="glyphicon glyphicon-trash"></span>',
                    Url::to(['/admin/schedule-trip/delete', 'id' => $model->id]),
                    [
                        'aria-label' => 'Удалить',
                        'data-method' => "post",
                        'data-pjax'  => 0,
                        'data-confirm' => "Вы уверены, что хотите удалить этот элемент?",
                    ]
                );
            },
        ],
    ];
}


echo GridView::widget([
    'dataProvider' => $dataTripProvider,
    //'filterModel' => $searchTripModel,
    'columns' => $columns,
]); ?>
