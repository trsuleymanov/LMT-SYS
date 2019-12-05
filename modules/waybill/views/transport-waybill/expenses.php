<?php

use app\models\TransportPaymentMethods;
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
use kartik\date\DatePicker;


$this->title = 'Перечень расходов';

$user = Yii::$app->user->identity;

$pagination = $dataProvider->getPagination();
$pagination->totalCount = $dataProvider->getTotalCount();

$this->registerCssFile('css/waybill-expenses.css', ['depends'=>'app\assets\AppAsset']);
$this->registerCssFile('css/waybill.css', ['depends'=>'app\assets\AppAsset']);
$this->registerJsFile('js/site/waybill-expenses.js', ['depends'=>'app\assets\AppAsset']);
$this->registerJsFile('js/site/waybill.js', ['depends'=>'app\assets\AppAsset']);


$exportColumns = [
    ['class' => 'kartik\grid\SerialColumn'],
];


$exportColumns[] = [
    'attribute' => 'waybill_number',
    'label' => '№ ПЛ',
    'content' => function ($model) {

        $waybill = $model->waybill;

        return
            $waybill->number
            .(!empty($waybill->trip_comment) ? $waybill->trip_comment : '');
    },
    'filter' => Html::activeTextInput($searchModel, 'waybill_number', ['class' => 'form-control'])
];





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
                    Url::to(['/waybill/transport-waybill/update', 'id' => $model->transport_waybill_id]),
                    [
                        'title' => 'Редактировать',
                        'aria-label' => 'Редактировать'
                    ]
                );
            },
        ],
    ],
];


$gridColumns[] = [
    'attribute' => 'id',
    'label' => 'id',
    'content' => function ($model) {
        return $model->id;
    },
    //'filter' => Html::activeTextInput($searchModel, 'waybill_number', ['class' => 'form-control'])
];


$gridColumns[] = [
    'attribute' => 'waybill_number',
    'label' => '№ ПЛ',
    'content' => function ($model) {

        $waybill = $model->waybill;

        return
            $waybill->number
            .(!empty($waybill->trip_comment) ? ' <span class="glyphicon glyphicon-share waybill-share-comment"></span><div class="waybill-comment">'.$waybill->trip_comment.'</div>' : '');
    },
    'filter' => Html::activeTextInput($searchModel, 'waybill_number', ['class' => 'form-control'])
];


$column = [
    'attribute' => 'waybill_date_of_issue',
    'label' => 'ДВ ПЛ',
    'content' => function ($model) {
        return ($model->waybill->date_of_issue > 0 ? date('d.m.Y', $model->waybill->date_of_issue) : '');
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


$column = [
    'attribute' => 'waybill_transport_id',
    'label' => 'Т/с',
    'content' => function ($model) {
        return ($model->waybill->transport_id > 0 ? $model->waybill->transport->sh_model.' '.$model->waybill->transport->car_reg : '');
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
$exportColumns[] = $column;
$gridColumns[] = $column;


$column = [
    'attribute' => 'waybill_driver_id',
    'label' => 'Водитель',
    'content' => function ($model) {
        //return ($model->waybill->driver_id > 0 ? $model->waybill->driver->fio : '');

        if($model->waybill->driver_id > 0) {
            if(mb_strlen($model->waybill->driver->fio, 'UTF-8') > 9) {
                return  mb_substr($model->waybill->driver->fio, 0, 9, 'UTF-8').'...';
            }else {
                return $model->waybill->driver->fio;
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



$exportColumns[] = [
    'attribute' => 'expenses_seller_type_id',
    'label' => 'За что',
    'content' => function ($model) {
        return $model->sellerType->name;
    },
    'filter' => SelectWidget::widget([
        'model' => $searchModel,
        'attribute' => 'expenses_seller_type_id',
        'initValueText' => ($searchModel->expenses_seller_type_id > 0 && $searchModel->sellerType != null ? $searchModel->sellerType->name : ''),
        'options' => [
            'placeholder' => 'Введите название...',
        ],
        'ajax' => [
            'url' => '/waybill/transport-waybill/ajax-get-sellers-types',
            'data' => new JsExpression('function(params, obj) {
                return {
                    search: params.search,
                };
            }'),
        ],
        'using_delete_button' => true,
    ])
];
$gridColumns[] = [
    'attribute' => 'expenses_seller_type_id',
    'label' => 'За что',
    'content' => function ($model) {
        return '<div style="min-width: 30px;">'.$model->sellerType->name.'</div>';
    },
    'filter' => SelectWidget::widget([
        'model' => $searchModel,
        'attribute' => 'expenses_seller_type_id',
        'initValueText' => ($searchModel->expenses_seller_type_id > 0 && $searchModel->sellerType != null ? $searchModel->sellerType->name : ''),
        'options' => [
            'placeholder' => 'Введите название...',
        ],
        'ajax' => [
            'url' => '/waybill/transport-waybill/ajax-get-sellers-types',
            'data' => new JsExpression('function(params, obj) {
                return {
                    search: params.search,
                };
            }'),
        ],
        'using_delete_button' => true,
    ])
];



$exportColumns[] = [
    'attribute' => 'expenses_seller_id',
    'label' => 'Продавец',
    'content' => function ($model) {
        return ($model->seller != null ? $model->seller->name : '');
    },
    'filter' => SelectWidget::widget([
        'model' => $searchModel,
        'attribute' => 'expenses_seller_id',
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
$gridColumns[] = [
    'attribute' => 'expenses_seller_id',
    'label' => 'Продавец',
    'content' => function ($model) {
        return ($model->seller != null ? $model->seller->name : '');
    },
    'filter' => SelectWidget::widget([
        'model' => $searchModel,
        'attribute' => 'expenses_seller_id',
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


$column = [
    'attribute' => 'price',
    'content' => function ($model) {
        return ($model->price > 0 ? $model->price : '');
    },
];
$exportColumns[] = $column;
$gridColumns[] = $column;


$column = [
    'label' => 'Рек. ДО', // группа полей
    'content' => function ($model) {
        return
            ($model->expenses_doc_type_id > 0 ? $model->docType->name.' ' : '')
            .($model->expenses_type_id > 0 ? $model->type->name.' ' : '')
            .(!empty($model->doc_number) ? '№ '.$model->doc_number : '');
    },
    //'filter' => Html::activeTextInput($searchModel, 'doc_number', ['class' => 'form-control'])
];
$exportColumns[] = $column;
$gridColumns[] = $column;


$column = [
    'attribute' => 'need_pay_date',
    'label' => 'Дата ДО',
    'content' => function ($model) {
        return ($model->need_pay_date > 0 ? date('d.m.Y', $model->need_pay_date) : '');
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





$exportColumns[] = [
    'label' => '?РП',
    'content' => function ($model) {
        return ($model->expenses_is_taken == 1 ? 'Да' : 'Нет');
    },
    'filter' => Html::activeDropDownList(
        $searchModel,
        'expenses_is_taken',
        ['' => 'Все', '0' => 'Нет', '1' => 'Да',],
        [
            'class' => "form-control",
            'style' => 'min-width: 100px;'
        ]
    )
];
//'options' => [
//    'id' => 'payment_date_'.$model->id,
//    'expense-id' => $model->id,
//    'field' => 'payment_date'
//]
$gridColumns[] = [
    'label' => '?РП',
    'content' => function ($model) {
        return Html::activeCheckbox($model, 'expenses_is_taken', [
            'label' => false,
            'name' => 'TransportExpenses['.$model->id.'][expenses_is_taken]',
            'id' => 'transportexpenses-expenses_is_taken-'.$model->id,
            'class' => 'transportexpenses-expenses_is_taken form-control',
            'field' => 'expenses_is_taken',
            'expense-id' => $model->id,

        ]);
    },
    'filter' => Html::activeDropDownList(
        $searchModel,
        'expenses_is_taken',
        ['' => 'Все', '0' => 'Нет', '1' => 'Да',],
        [
            'class' => "form-control",
            'style' => 'min-width: 100px;'
        ]
    )
];



$exportColumns[] = [
    //'label' => 'ЧРП, КРП',
    //'label' => 'Статус РП',
    'label' => 'Оплата',
    'content' => function ($model) {
        // Чек - check_attached
        // ?РП - expenses_is_taken
        // КРП - expenses_is_taken_comment
        return
            ($model->check_attached != true ? 'color: red;' : '')
            .'Чек - '.($model->check_attached == true ? 'да, ' : 'нет, ')
            //.'?РП - '.($model->expenses_is_taken == true ? 'да' : 'нет').', '
            .($model->payment_date > 0 && $model->payment_method_id > 0 && $model->transport_expenses_paymenter_id > 0 ? 'оплачено' : 'не оплачено');
    },
    'filter' => Html::activeDropDownList(
        $searchModel,
        'is_payed',
        //['' => 'Все', 'is_paid' => 'Оплачено', 'not_paid' => 'Не оплачено', 'accepted' => 'Принято', 'not_accepted' => 'Не принято'],
        ['' => 'Все', 'is_paid' => 'Оплачено', 'not_paid' => 'Не оплачено',],
        [
            'class' => "form-control",
            'style' => 'min-width: 100px;'
        ]
    )
];
$gridColumns[] = [
    //'label' => 'ЧРП, КРП',
    //'label' => 'Статус РП',
    'label' => 'Оплата',
    'content' => function ($model) {
        // Чек - check_attached
        // ?РП - expenses_is_taken
        // КРП - expenses_is_taken_comment
        return
            '<span style="'.($model->check_attached != true ? 'color: red;' : '').'">'
            .'Чек - '.($model->check_attached == true ? 'да, ' : 'нет, ')
            //.'?РП - '.($model->expenses_is_taken == true ? 'да' : 'нет').', '
            .($model->payment_date > 0 && $model->payment_method_id > 0 && $model->transport_expenses_paymenter_id > 0 ? 'оплачено' : 'не оплачено')
            //.'<br /><br />'.$model->expenses_is_taken_comment
            .'</span>';
    },
    'filter' => Html::activeDropDownList(
        $searchModel,
        'is_payed',
        //['' => 'Все', 'is_paid' => 'Оплачено', 'not_paid' => 'Не оплачено', 'accepted' => 'Принято', 'not_accepted' => 'Не принято'],
        ['' => 'Все', 'is_paid' => 'Оплачено', 'not_paid' => 'Не оплачено',],
        [
            'class' => "form-control",
            'style' => 'min-width: 100px;'
        ]
    )
];


$exportColumns[] = [
    'attribute' => 'payment_date',
    'content' => function ($model) {
        return ($model->payment_date > 0 ? date('d.m.Y', $model->payment_date) : '');
    },
    'filter' => PeriodPicker::widget([
        'model' => $searchModel,
        'attribute' => 'payment_date',
        'isFilterInGridView' => true,
        'clearButton' => false
    ]),
];

$gridColumns[] = [
    'attribute' => 'payment_date',
    'content' => function ($model) {
        if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin'])) {

            if($model->payment_date > 0 && !preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$/i', $model->payment_date)) {
                $model->payment_date = date("d.m.Y", $model->payment_date);
            }
            return DatePicker::widget([
                'model' => $model,
                'attribute' => 'payment_date',
                'type' => DatePicker::TYPE_INPUT,
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'dd.mm.yyyy',
                ],
                'options' => [
                    'id' => 'payment_date_'.$model->id,
                    'expense-id' => $model->id,
                    'field' => 'payment_date'
                ]
            ]);
        }else {
            return ($model->payment_date > 0 ? date('d.m.Y', $model->payment_date) : '');
        }
    },
    'filter' => PeriodPicker::widget([
        'model' => $searchModel,
        'attribute' => 'payment_date',
        'isFilterInGridView' => true,
        'clearButton' => false
    ]),
];



$exportColumns[] = [
    'attribute' => 'payment_method_id',
    'content' => function ($model) {
        return ($model->payment_method_id > 0 ? $model->paymentMethod->name : '');
    },
    'filter' => Html::activeDropDownList(
        $searchModel,
        'payment_method_id',
        ['' => 'Все'] + ArrayHelper::map(TransportPaymentMethods::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),
        [
            'class' => "form-control",
            'style' => 'min-width: 100px;'
        ]
    )
];
$gridColumns[] = [
    'attribute' => 'payment_method_id',
    'content' => function ($model) {
        if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin'])) {
            return Html::activeDropDownList($model, 'payment_method_id', ArrayHelper::map(TransportPaymentMethods::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'), [
                'class' => "form-control",
                'style' => 'min-width: 100px;',
                'expense-id' => $model->id,
                'field' => 'payment_method_id'
            ]);
        }else {
            return ($model->payment_method_id > 0 ? $model->paymentMethod->name : '');
        }
    },
    'filter' => Html::activeDropDownList(
        $searchModel,
        'payment_method_id',
        ['' => 'Все'] + ArrayHelper::map(TransportPaymentMethods::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),
        [
            'class' => "form-control",
            'style' => 'min-width: 100px;'
        ]
    )
];


$exportColumns[] = [
    'attribute' => 'transport_expenses_paymenter_id',
    'content' => function ($model) {
        return ($model->transport_expenses_paymenter_id > 0 ? $model->paymenter->lastname . ' ' . $model->paymenter->firstname : '');
    },
    'filter' => SelectWidget::widget([
        'model' => $searchModel,
        'attribute' => 'transport_expenses_paymenter_id',
        'initValueText' => ($searchModel->transport_expenses_paymenter_id > 0 && $searchModel->paymenter != null ? $searchModel->paymenter->lastname.' '.$searchModel->paymenter->firstname : ''),
        'options' => [
            'placeholder' => 'Введите название...',
        ],
        'ajax' => [
            'url' => '/user/ajax-get-users',
            'data' => new JsExpression('function(params, obj) {
                return {
                    search: params.search,
                };
            }'),
        ],
        'using_delete_button' => true
    ])
];

$gridColumns[] = [
    'attribute' => 'transport_expenses_paymenter_id',
    'content' => function ($model) {
        if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin'])) {

            return SelectWidget::widget([
                'model' => $model,
                'attribute' => 'transport_expenses_paymenter_id',
                'initValueText' => ($model->transport_expenses_paymenter_id > 0 && $model->paymenter != null ? $model->paymenter->lastname.' '.$model->paymenter->firstname : ''),
                'options' => [
                    'placeholder' => 'Введите название...',
                    'expense-id' => $model->id,
                    'field' => 'transport_expenses_paymenter_id'
                ],
                'ajax' => [
                    'url' => '/user/ajax-get-users',
                    'data' => new JsExpression('function(params, obj) {
                        return {
                            search: params.search,
                        };
                    }'),
                ],
                'using_delete_button' => true
            ]);

        }else {
            return ($model->transport_expenses_paymenter_id > 0 ? $model->paymenter->lastname . ' ' . $model->paymenter->firstname : '');
        }
    },
    'filter' => SelectWidget::widget([
        'model' => $searchModel,
        'attribute' => 'transport_expenses_paymenter_id',
        'initValueText' => ($searchModel->transport_expenses_paymenter_id > 0 && $searchModel->paymenter != null ? $searchModel->paymenter->lastname.' '.$searchModel->paymenter->firstname : ''),
        'options' => [
            'placeholder' => 'Введите название...',
        ],
        'ajax' => [
            'url' => '/user/ajax-get-users',
            'data' => new JsExpression('function(params, obj) {
                return {
                    search: params.search,
                };
            }'),
        ],
        'using_delete_button' => true
    ])
];


$exportColumns[] = [
    'label' => 'КО',
    'attribute' => 'payment_comment',
    'content' => function ($model) {
        return (!empty($model->payment_comment) ? $model->payment_comment : '');
    }
];
$gridColumns[] = [
    'label' => 'КО',
    'attribute' => 'payment_comment',
    'content' => function ($model) {

        if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin'])) {
            return '<textarea style="min-width:100px;" expense-id="'.$model->id.'" field="payment_comment" class="form-control">'.$model->payment_comment.'</textarea>';
        }else {
            return (!empty($model->payment_comment) ? $model->payment_comment : '');
        }
    }
];

//'count',
//'points',
//'payment_method_id',
//'transport_expenses_paymenter_id',
?>

<?= $this->render('_menu') ?>

<h2><?= Html::encode($this->title) ?></h2>
<div id="transport-expenses-index" class="box box-default">

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
        //Yii::$app->session->set('table-rows', 20);
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
//                            Url::to(['/waybill/transport-waybill/update', 'id' => $model->transport_waybill_id]),
//                            [
//                                'title' => 'Редактировать',
//                                'aria-label' => 'Редактировать'
//                            ]
//                        );
//                    },
//                ],
//            ],
//        ];

        //$gridColumns = array_merge($gridColumns, $columns);

        echo GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'layout'=>"{summary}\n<div class='expenses-data-table-over'><div class='expenses-data-table-inner'>{items}</div></div>",
            'options' => [
                'class' => 'grid-view table-responsive',
                //'id' => 'table-block'
            ],
            'tableOptions' => [
                'class' => 'table table-striped table-bordered',
                'id' => 'expenses-data-table'
            ],
            'rowOptions' => function ($model, $key, $index, $grid) {

                $style = '';
                if(empty($model->payment_date)) {
                    $style = 'background-color: #FFFF00;';
                }

                return [
                    'style' => $style,
                ];
            },
            'columns' => $gridColumns,
            'showFooter' => true,
        ]);
        ?>

        <?php
        echo Html::a('Сохранить изменения', ['#'], ['id' => 'save-expenses-table', 'class' => 'btn btn-success']);
        ?>
    </div>
</div>