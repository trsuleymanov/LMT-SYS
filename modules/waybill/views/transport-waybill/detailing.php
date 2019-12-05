<?php

use app\models\TransportExpensesDetailing;
use app\models\TransportExpensesDocType;
use app\models\TransportExpensesTypes;
use kartik\date\DatePicker;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use app\helpers\table\PageSizeHelper;
use yii\web\ForbiddenHttpException;
use yii\web\JsExpression;
use yii\widgets\LinkPager;
use app\widgets\periodPicker\PeriodPicker;
use kartik\export\ExportMenu;
use yii\helpers\Url;
use app\widgets\SelectWidget;


$this->title = 'Детализация расходов';

$user = Yii::$app->user->identity;

$pagination = $dataProvider->getPagination();
$pagination->totalCount = $dataProvider->getTotalCount();

$this->registerCssFile('css/waybill-detailing.css', ['depends'=>'app\assets\AppAsset']);
$this->registerCssFile('css/waybill.css', ['depends'=>'app\assets\AppAsset']);
$this->registerJsFile('js/site/waybill-detailing.js', ['depends'=>'app\assets\AppAsset']);
$this->registerJsFile('js/site/waybill.js', ['depends'=>'app\assets\AppAsset']);


//$expenses_seller_id = 2;
//$detailing = TransportExpensesDetailing::find()->where(['id' => $expenses_seller_id])->one();
//$expenses = $detailing->expenses;
//$seller = $expenses->seller;
//
//echo "seller:<pre>"; print_r($seller); echo "</pre>";


$gridColumns = [
    [
        'content' => function ($model, $key, $index) {
            return $key;
        },
        'header' => '#',
    ],

    [
        'class' => 'yii\grid\ActionColumn',
        'template' => '{update}',
        'options' => ['style' => 'width: 25px;'],
        'buttons' => [
            'update' => function ($url, $model)
            {
                return Html::a(
                    '<span class="glyphicon glyphicon-pencil"></span>',
                    Url::to(['/waybill/transport-waybill/update', 'id' => $model->expenses->transport_waybill_id]),
                    [
                        'title' => 'Редактировать',
                        'aria-label' => 'Редактировать'
                    ]
                );
            },
        ],
    ],
];

$exportColumns = [
    ['class' => 'kartik\grid\SerialColumn'],
];


$column = [
    'attribute' => 'waybill_number',
    'label' => '№ ПЛ',
    'content' => function ($model) {
        return $model->expenses->waybill->number;
    },
    'filter' => Html::activeTextInput($searchModel, 'waybill_number', ['class' => 'form-control'])
];
$exportColumns[] = $column;
$gridColumns[] = $column;


$column = [
    'attribute' => 'waybill_date_of_issue',
    'label' => 'ДВ ПЛ',
    'content' => function ($model) {
        return ($model->expenses->waybill->date_of_issue > 0 ? date('d.m.Y', $model->expenses->waybill->date_of_issue) : '');
    },
    'filter' => PeriodPicker::widget([
        'model' => $searchModel,
        'attribute' => 'waybill_date_of_issue',
        'isFilterInGridView' => true,
        'clearButton' => false
    ]),
];
$exportColumns[] = $column;
$gridColumns[] = $column;


$exportColumns[] = [
    'attribute' => 'waybill_transport_id',
    'label' => 'Т/с',
    'content' => function ($model) {
        return ($model->expenses->waybill->transport_id > 0 ? $model->expenses->waybill->transport->sh_model.' '.$model->expenses->waybill->transport->car_reg : '');
    },
    'filter' => SelectWidget::widget([
        'model' => $searchModel,
        'attribute' => 'waybill_transport_id',
        'initValueText' => ($searchModel->waybill_transport_id > 0 ? $searchModel->transport->name3 : ''),
        'options' => [
            'placeholder' => 'Введите название...',
        ],
        'ajax' => [
            'url' => '/trip-transport/ajax-get-transports-names',
            'data' => new JsExpression('function(params) {
                return {
                    search: params.search,
                    format: "name3"
                };
            }'),
        ],
        //'using_delete_button' => false
    ])
];
$gridColumns[] = [
    'attribute' => 'waybill_transport_id',
    'label' => 'Т/с',
    'content' => function ($model) {
        return '<div style="min-width: 100px;">'.($model->expenses->waybill->transport_id > 0 ? $model->expenses->waybill->transport->sh_model.' '.$model->expenses->waybill->transport->car_reg : '').'</div>';
    },
    'filter' => SelectWidget::widget([
        'model' => $searchModel,
        'attribute' => 'waybill_transport_id',
        'initValueText' => ($searchModel->waybill_transport_id > 0 ? $searchModel->transport->name3 : ''),
        'options' => [
            'placeholder' => 'Введите название...',
        ],
        'ajax' => [
            'url' => '/trip-transport/ajax-get-transports-names',
            'data' => new JsExpression('function(params) {
                return {
                    search: params.search,
                    format: "name3"
                };
            }'),
        ],
        //'using_delete_button' => false
    ])
];


$column = [
    'attribute' => 'waybill_driver_id',
    'label' => 'Водитель',
    'content' => function ($model) {
        //return ($model->expenses->waybill->driver_id > 0 ? $model->expenses->waybill->driver->fio : '');
        if($model->expenses->waybill->driver_id > 0) {
            if(mb_strlen($model->expenses->waybill->driver->fio, 'UTF-8') > 9) {
                return  mb_substr($model->expenses->waybill->driver->fio, 0, 9, 'UTF-8').'...';
            }else {
                return $model->expenses->waybill->driver->fio;
            }
        }else {
            return '';
        }
    },
    'filter' => SelectWidget::widget([
        'model' => $searchModel,
        'attribute' => 'waybill_driver_id',
        'name' => 'driver_id',
        'initValueText' => ($searchModel->waybill_driver_id > 0 ? $searchModel->driver->fio : ''),
        'options' => [
            'placeholder' => 'Введите название...',
        ],
        'ajax' => [
            'url' => '/trip-transport/ajax-get-drivers-names',
            'data' => new JsExpression('function(params, $obj) {
                return {
                    search: params.search
                };
            }'),
        ],
        //'using_delete_button' => false
    ])
];
$exportColumns[] = $column;
$gridColumns[] = $column;


$column = [
    'attribute' => 'need_pay_date',
    'label' => 'Дата ДО',
    'content' => function ($model) {
        return ($model->expenses->need_pay_date > 0 ? date('d.m.Y', $model->expenses->need_pay_date) : '');
    },
    'filter' => PeriodPicker::widget([
        'model' => $searchModel,
        'attribute' => 'need_pay_date',
        'isFilterInGridView' => true,
        'clearButton' => false
    ]),
];
$exportColumns[] = $column;
$gridColumns[] = $column;



$column = [
    'attribute' => 'expenses_seller_id',
    'label' => 'Продавец',
    'content' => function ($model) {
        return ($model->expenses->seller != null ? $model->expenses->seller->name : '');
    },
    'filter' => SelectWidget::widget([
        'model' => $searchModel,
        'attribute' => 'expenses_seller_id',
        //'id' => 'transportexpenses-expenses_seller_id-'.$model->id,
        //'name' => 'TransportExpenses['.$searchModel->id.'][expenses_seller_id]',
        'initValueText' => ($searchModel->expenses_seller_id > 0 ? $searchModel->seller->name : ''),
        'options' => [
            'placeholder' => 'Введите название...',
            'class' => 'transportexpenses-expenses_seller_id',
        ],
        'ajax' => [
            'url' => '/waybill/transport-expenses-detailing/ajax-get-sellers-names',
            'data' => new JsExpression('function(params, obj) {
                return {
                    search: params.search,
                };
            }'),
        ],
        'using_delete_button' => true
    ])
];
$exportColumns[] = $column;
$gridColumns[] = $column;


$column = [
    'attribute' => 'expenses_doc_type_id',
    'label' => 'Вид ДО', // expenses_doc_type_id
    'content' => function ($model) {
        return ($model->expenses->expenses_doc_type_id > 0 ? $model->expenses->docType->name : '');
    },
    'filter' => Html::activeDropDownList(
        $searchModel,
        'expenses_doc_type_id',
        ['' => 'Все'] + ArrayHelper::map(TransportExpensesDocType::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),
        [
            'class' => "form-control",
            'style' => 'min-width: 100px;'
        ]
    )
];
$exportColumns[] = $column;
$gridColumns[] = $column;


$column = [
    'attribute' => 'expenses_type_id',
    'label' => 'ДО', // expenses_type_id
    'content' => function ($model) {
        return ($model->expenses->expenses_type_id > 0 ? $model->expenses->type->name : '');
    },
    'filter' => Html::activeDropDownList(
        $searchModel,
        'expenses_type_id',
        ['' => 'Все'] + ArrayHelper::map(TransportExpensesTypes::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),
        [
            'class' => "form-control",
            'style' => 'min-width: 100px;'
        ]
    )
];
$exportColumns[] = $column;
$gridColumns[] = $column;


$column = [
    'attribute' => 'doc_number',
    'label' => '№ ДО', // doc_number
    'content' => function ($model) {
        return (!empty($model->expenses->doc_number) ? $model->expenses->doc_number : '');
    },
    'filter' => Html::activeTextInput($searchModel, 'doc_number', ['class' => 'form-control'])
];
$exportColumns[] = $column;
$gridColumns[] = $column;


$exportColumns[] = 'name';
$gridColumns[] = 'name';


$exportColumns[] = 'price';
$gridColumns[] = 'price';


$column = [
    'attribute' => 'type',
    'label' => 'Тип',
    'content' => function ($model) {
        return (!empty($model->type) ? TransportExpensesDetailing::getTypes()[$model->type] : '');
    },
    'filter' => Html::activeDropDownList(
        $searchModel,
        'type',
        ['' => 'Все'] + TransportExpensesDetailing::getTypes(),
        [
            'class' => "form-control",
            'style' => 'min-width: 100px;'
        ]
    )
];
$exportColumns[] = $column;
$gridColumns[] = $column;

?>

<?= $this->render('_menu') ?>

<h2><?= Html::encode($this->title) ?></h2>
<div id="transport-expenses-detailing-index" class="box box-default">

    <div class="box-header scroller with-border">
        <div class="pull-left">
            <?php
            //echo Html::a('<i class="glyphicon glyphicon-plus"></i> Создать путевой лист', ['create'], ['class' => 'btn btn-success']);
            ?>
        </div>

        <br />
        <div class="pull-left">
            <?php
//            $gridColumns = [
//                ['class' => 'kartik\grid\SerialColumn'],
//            ];
//            $gridColumns = array_merge($gridColumns, $columns);

            echo '<div style="vertical-align: top; display: inline-block;">'.ExportMenu::widget([
                    'dataProvider' => $dataProvider,
                    'columns' => $exportColumns,
                    'fontAwesome' => true
                ]).'</div>';
            ?>
        </div>

        <div class="pull-left">
            <?php
            if(empty(Yii::$app->session->get('table-rows'))) {
                Yii::$app->session->set('table-rows', 20);
            }

            echo LinkPager::widget([
                'pagination' => $pagination,
                'options' => [
                    'class' => 'pagination pagination-sm',
                    'style' => 'margin: 4px 20px 0 30px;'
                ]
            ]);
            ?>
        </div>
        <?php
        echo (new PageSizeHelper([10, 20, 50, 100, 200, 500]))->getButtons();
        ?>
    </div>

    <br /><br />
    <div class="box-body box-table" style="clear: both;">

        <?php

//        $gridColumns = [
//            [
//                'content' => function ($model, $key, $index) {
//                    return $key;
//                },
//                'header' => '#',
//            ],
//
//            [
//                'class' => 'yii\grid\ActionColumn',
//                'template' => '{update}',
//                'options' => ['style' => 'width: 25px;'],
//                'buttons' => [
//                    'update' => function ($url, $model)
//                    {
//                        return Html::a(
//                            '<span class="glyphicon glyphicon-pencil"></span>',
//                            Url::to(['/waybill/transport-waybill/update', 'id' => $model->expenses->transport_waybill_id]),
//                            [
//                                'title' => 'Редактировать',
//                                'aria-label' => 'Редактировать'
//                            ]
//                        );
//                    },
//                ],
//            ],
//        ];
//
//        $gridColumns = array_merge($gridColumns, $columns);

        echo GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'layout'=>"{summary}\n<div class='detailing-data-table-over'><div class='detailing-data-table-inner'>{items}</div></div>",
            'options' => [
                'class' => 'grid-view table-responsive',
                //'id' => 'table-block'
            ],
            'tableOptions' => [
                //'class' => 'table table-condensed table-bordered table-hover',
                'class' => 'table table-striped table-bordered',
                'id' => 'detailing-data-table'
            ],
            'columns' => $gridColumns,
            'showFooter' => true,
        ]);
        ?>
    </div>
</div>