<?php

use app\models\TransportExpensesDetailing;
use app\models\TransportExpensesSellerType;
use app\models\TransportWaybill;
use yii\helpers\Html;
use yii\grid\GridView;
use app\helpers\table\PageSizeHelper;
use yii\widgets\LinkPager;
use app\widgets\periodPicker\PeriodPicker;
use yii\helpers\Url;
use app\widgets\SelectWidget;
use yii\web\JsExpression;
use kartik\export\ExportMenu;


$this->title = 'Список путевых листов';

//$this->params['breadcrumbs'][] = $this->title;


$pagination = $dataProvider->getPagination();
$pagination->totalCount = $dataProvider->getTotalCount();

$this->registerCssFile('css/waybill.css', ['depends'=>'app\assets\AppAsset']);
$this->registerCssFile('css/waybill-list.css', ['depends'=>'app\assets\AppAsset']);
$this->registerJsFile('js/site/waybill.js', ['depends'=>'app\assets\AppAsset']);
$this->registerJsFile('js/site/waybill-list.js', ['depends'=>'app\assets\AppAsset']);

$stoyanka_expenses_seller_type = TransportExpensesSellerType::find()->where(['name' => 'Стоянка'])->one();
$azs_expenses_seller_type = TransportExpensesSellerType::find()->where(['name' => 'АЗС'])->one();
$moika_expenses_seller_type = TransportExpensesSellerType::find()->where(['name' => 'Мойка'])->one();



$exportColumns = [
    ['class' => 'kartik\grid\SerialColumn'],
];


$gridColumns = [];
$gridColumns[] =
    [
        'content' => function ($model, $key, $index) {
            return $key;
        },
        'header' => '#',
    ];
//
//$gridColumns[] = [
//    'attribute' => 'is_visible',
//    'label' => '',
//    'content' => function ($model) {
//
//        if($model->is_visible == true) {
//            return '<a href="" class="glyphicon glyphicon-eye-open"></a>';
//        }else {
//            return '<a href="" class="glyphicon glyphicon-eye-close"></a>';
//        }
//    },
//    'filter' => false
//];

if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin'])) {
    if (isset($_GET['TransportWaybillSearch']['is_visible']) && $_GET['TransportWaybillSearch']['is_visible'] == 0) {
        $is_visible_col_filter = '<a id="show-all-waybills" href="" title="Показать все ПЛ"><span class="glyphicon glyphicon-eye-open"><br /><span class="glyphicon glyphicon-eye-close"></span></a>';
    } else {
        $is_visible_col_filter = '<a id="show-hidden-waybills" href="" title="Показать только скрытые ПЛ"><span class="glyphicon glyphicon-eye-close"></span></a>';
    }
}else {
    $is_visible_col_filter = '';
}

$gridColumns[] =
    [
        'class' => 'yii\grid\ActionColumn',
        'header' => $is_visible_col_filter,
        'template' => '{update}',
        'options' => ['style' => 'width: 25px;'],
        'buttons' => [
            'update' => function ($url, $model)
            {
                $html = Html::a(
                    '<span class="glyphicon glyphicon-pencil"></span>',
                    Url::to(['/waybill/transport-waybill/update', 'id' => $model->id]),
                    [
                        'title' => 'Редактировать',
                        'aria-label' => 'Редактировать'
                    ]
                );

                if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin'])) {
                    if ($model->is_visible == true) {
                        $html .= '<a href="" class="to-hide-pl glyphicon glyphicon-eye-open"></a>';
                    } else {
                        $html .= '<a href="" class="to-show-pl glyphicon glyphicon-eye-close"></a>';
                    }
                }

                return $html;
            },
        ],
    ];



$exportColumns[] = [
    'attribute' => 'number',
    'label' => '№ ПЛ',
    'content' => function ($model) {
        return
            $model->number
            .(!empty($model->trip_comment) ? ' '.$model->trip_comment : '');
    },
    'filter' => false
];
$gridColumns[] = [
    'attribute' => 'number',
    'label' => '№ ПЛ',
    'content' => function ($model) {
        return
            $model->number
            .(!empty($model->trip_comment) ? ' <span class="glyphicon glyphicon-share waybill-share-comment"></span><div class="waybill-comment">'.$model->trip_comment.'</div>' : '');
    }
];

$column = [
    'attribute' => 'date_of_issue',
    'label' => 'ДВ ПЛ',
    'content' => function ($model) {
        return ($model->date_of_issue > 0 ? date('d.m.Y', $model->date_of_issue) : '');
    },
    'filter' => PeriodPicker::widget([
        'model' => $searchModel,
        'attribute' => 'date_of_issue',
        'isFilterInGridView' => true,
        'clearButton' => false
    ]),
];
$exportColumns[] = $column;
$gridColumns[] = $column;


$gridColumns[] = [
    'attribute' => 'transport_id',
    'label' => 'Т/с',
    'content' => function ($model) {
        return '<div style="min-width: 50px;">'.($model->transport_id > 0 ? $model->transport->sh_model.' '.$model->transport->car_reg : '').'</div>';
    },
    'filter' => SelectWidget::widget([
        'model' => $searchModel,
        'attribute' => 'transport_id',
        'initValueText' => ($searchModel->transport_id > 0 ? $searchModel->transport->name3 : ''),
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
        'using_delete_button' => true
    ])
];
$exportColumns[] = [
    'attribute' => 'transport_id',
    'label' => 'Т/с',
    'content' => function ($model) {
        return ($model->transport_id > 0 ? $model->transport->sh_model.' '.$model->transport->car_reg : '');
    },
//    'filter' => SelectWidget::widget([
//        'model' => $searchModel,
//        'attribute' => 'transport_id',
//        'initValueText' => ($searchModel->transport_id > 0 ? $searchModel->transport->name3 : ''),
//        'options' => [
//            'placeholder' => 'Введите название...',
//        ],
//        'ajax' => [
//            'url' => '/trip-transport/ajax-get-transports-names',
//            'data' => new JsExpression('function(params) {
//                return {
//                    search: params.search,
//                    format: "name3"
//                };
//            }'),
//        ],
//        'using_delete_button' => true
//    ])
];




$gridColumns[] = [
    'attribute' => 'driver_id',
    'label' => 'Водитель',
    'content' => function ($model) {

        $fio = '';
        if($model->driver_id > 0 && $model->driver != null) {
            if(mb_strlen($model->driver->fio, 'UTF-8') > 9) {
                $fio =  mb_substr($model->driver->fio, 0, 9, 'UTF-8').'...';
            }else {
                $fio = $model->driver->fio;
            }
        }

        return '<div style="min-width: 100px; ">'.$fio.'</div>';
    },
    'filter' => SelectWidget::widget([
        'model' => $searchModel,
        'attribute' => 'driver_id',
        //'name' => 'driver_id',
        'initValueText' => ($searchModel->driver_id > 0 ? $searchModel->driver->fio : ''),
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
        'using_delete_button' => true
    ])
];
$exportColumns[]  = [
    'attribute' => 'driver_id',
    'label' => 'Водитель',
    'content' => function ($model) {

        $fio = '';
        if($model->driver_id > 0) {
            if(mb_strlen($model->driver->fio, 'UTF-8') > 9) {
                $fio =  mb_substr($model->driver->fio, 0, 9, 'UTF-8').'...';
            }else {
                $fio = $model->driver->fio;
            }
        }

        return $fio;
    },
];




$column = [
    'label' => 'Состав',
    'content' => function ($model) {

        $string = '';

        if(empty($model->trip_transport_start)) {
            $string .= 'НЕТ';
        }else {
            $trip_transport_start = $model->tripTransportStart;
            $trip_start = $trip_transport_start->trip;
            $string .= ($trip_start->direction_id == 1 ? 'АК ' : 'КА ').' '.$trip_start->name;
        }

        $string .= ' - ';

        if(empty($model->trip_transport_end)) {
            $string .= 'НЕТ';
        }else {
            $trip_transport_start = $model->tripTransportEnd;
            $trip_start = $trip_transport_start->trip;
            $string .= ($trip_start->direction_id == 1 ? 'АК ' : 'КА ').' '.$trip_start->name.' '.date('d.m.Y', $trip_start->date);
        }

        return $string;
    },
];
$exportColumns[] = $column;
$gridColumns[] = $column;


if(!in_array(Yii::$app->session->get('role_alias'), ['warehouse_turnover'])) { // Эксплуатация не видит этих колонок


    $exportColumns[] = [
        'label' => 'М', //всего мест на двух рейсах
        'content' => function ($model) {

            $day_report_trip_transport_start = null;
            $day_report_trip_transport_end = null;
            $trip_transport_start = $model->tripTransportStart;
            if ($trip_transport_start != null) {
                $day_report_trip_transport_start = $trip_transport_start->dayReportTripTransport;
            }
            $trip_transport_end = $model->tripTransportEnd;
            if ($trip_transport_end != null) {
                $day_report_trip_transport_end = $trip_transport_end->dayReportTripTransport;
            }

            $places_count_sent = 0;
            if ($day_report_trip_transport_start != null) {
                $places_count_sent += $day_report_trip_transport_start->places_count_sent;
            }
            if ($day_report_trip_transport_end != null) {
                $places_count_sent += $day_report_trip_transport_end->places_count_sent;
            }


            return
                " "
                .$places_count_sent
                . (!empty($model->correct_comment) ? $model->correct_comment : '');
        },
        'filter' => false
    ];
    $gridColumns[] = [
        'label' => 'М', //всего мест на двух рейсах
        'content' => function ($model) {

            $day_report_trip_transport_start = null;
            $day_report_trip_transport_end = null;
            $trip_transport_start = $model->tripTransportStart;
            if ($trip_transport_start != null) {
                $day_report_trip_transport_start = $trip_transport_start->dayReportTripTransport;
            }
            $trip_transport_end = $model->tripTransportEnd;
            if ($trip_transport_end != null) {
                $day_report_trip_transport_end = $trip_transport_end->dayReportTripTransport;
            }

            $places_count_sent = 0;
            if ($day_report_trip_transport_start != null) {
                $places_count_sent += $day_report_trip_transport_start->places_count_sent;
            }
            if ($day_report_trip_transport_end != null) {
                $places_count_sent += $day_report_trip_transport_end->places_count_sent;
            }


            return
                $places_count_sent
                . (!empty($model->correct_comment) ? ' <span class="glyphicon glyphicon-share waybill-share-comment"></span><div class="waybill-comment">' . $model->correct_comment . '</div>' : '');
        }
    ];

    $column = [
        'label' => 'Д',
        'content' => function ($model) {

            $day_report_trip_transport_start = null;
            $day_report_trip_transport_end = null;
            $trip_transport_start = $model->tripTransportStart;
            if ($trip_transport_start != null) {
                $day_report_trip_transport_start = $trip_transport_start->dayReportTripTransport;
            }
            $trip_transport_end = $model->tripTransportEnd;
            if ($trip_transport_end != null) {
                $day_report_trip_transport_end = $trip_transport_end->dayReportTripTransport;
            }

            $child_count_sent = 0;
            if ($day_report_trip_transport_start != null) {
                $child_count_sent += $day_report_trip_transport_start->child_count_sent;
            }
            if ($day_report_trip_transport_end != null) {
                $child_count_sent += $day_report_trip_transport_end->child_count_sent;
            }

            return $child_count_sent;
        }
    ];
    $exportColumns[] = $column;
    $gridColumns[] = $column;

    $column = [
        'label' => 'С',
        'content' => function ($model) {

            $day_report_trip_transport_start = null;
            $day_report_trip_transport_end = null;
            $trip_transport_start = $model->tripTransportStart;
            if ($trip_transport_start != null) {
                $day_report_trip_transport_start = $trip_transport_start->dayReportTripTransport;
            }
            $trip_transport_end = $model->tripTransportEnd;
            if ($trip_transport_end != null) {
                $day_report_trip_transport_end = $trip_transport_end->dayReportTripTransport;
            }

            $student_count_sent = 0;
            if ($day_report_trip_transport_start != null) {
                $student_count_sent += $day_report_trip_transport_start->student_count_sent;
            }
            if ($day_report_trip_transport_end != null) {
                $student_count_sent += $day_report_trip_transport_end->student_count_sent;
            }

            return $student_count_sent;
        }
    ];
    $exportColumns[] = $column;
    $gridColumns[] = $column;

    $column = [
        'label' => 'А',
        'content' => function ($model) {

            $day_report_trip_transport_start = null;
            $day_report_trip_transport_end = null;
            $trip_transport_start = $model->tripTransportStart;
            if ($trip_transport_start != null) {
                $day_report_trip_transport_start = $trip_transport_start->dayReportTripTransport;
            }
            $trip_transport_end = $model->tripTransportEnd;
            if ($trip_transport_end != null) {
                $day_report_trip_transport_end = $trip_transport_end->dayReportTripTransport;
            }

            $airport_count_sent = 0;
            if ($day_report_trip_transport_start != null) {
                $airport_count_sent += $day_report_trip_transport_start->airport_count_sent;
            }
            if ($day_report_trip_transport_end != null) {
                $airport_count_sent += $day_report_trip_transport_end->airport_count_sent;
            }

            return $airport_count_sent;
        }
    ];
    $exportColumns[] = $column;
    $gridColumns[] = $column;

    $column = [
        'label' => 'П',
        'content' => function ($model) {

            $day_report_trip_transport_start = null;
            $day_report_trip_transport_end = null;
            $trip_transport_start = $model->tripTransportStart;
            if ($trip_transport_start != null) {
                $day_report_trip_transport_start = $trip_transport_start->dayReportTripTransport;
            }
            $trip_transport_end = $model->tripTransportEnd;
            if ($trip_transport_end != null) {
                $day_report_trip_transport_end = $trip_transport_end->dayReportTripTransport;
            }

            $prize_trip_count_sent = 0;
            if ($day_report_trip_transport_start != null) {
                $prize_trip_count_sent += $day_report_trip_transport_start->prize_trip_count_sent;
            }
            if ($day_report_trip_transport_end != null) {
                $prize_trip_count_sent += $day_report_trip_transport_end->prize_trip_count_sent;
            }

            return $prize_trip_count_sent;
        }
    ];
    $exportColumns[] = $column;
    $gridColumns[] = $column;

    $column = [
        'label' => 'БМ',// Всего без места
        'content' => function ($model) {

            $day_report_trip_transport_start = null;
            $day_report_trip_transport_end = null;
            $trip_transport_start = $model->tripTransportStart;
            if ($trip_transport_start != null) {
                $day_report_trip_transport_start = $trip_transport_start->dayReportTripTransport;
            }
            $trip_transport_end = $model->tripTransportEnd;
            if ($trip_transport_end != null) {
                $day_report_trip_transport_end = $trip_transport_end->dayReportTripTransport;
            }

            $is_not_places_count_sent = 0;
            if ($day_report_trip_transport_start != null) {
                $is_not_places_count_sent += $day_report_trip_transport_start->is_not_places_count_sent;
            }
            if ($day_report_trip_transport_end != null) {
                $is_not_places_count_sent += $day_report_trip_transport_end->is_not_places_count_sent;
            }

            return $is_not_places_count_sent;
        }
    ];
    $exportColumns[] = $column;
    $gridColumns[] = $column;

    $column = [
        'label' => 'Lug',// Всего багажа
        'content' => function ($model) {

            $day_report_trip_transport_start = null;
            $day_report_trip_transport_end = null;
            $trip_transport_start = $model->tripTransportStart;
            if ($trip_transport_start != null) {
                $day_report_trip_transport_start = $trip_transport_start->dayReportTripTransport;
            }
            $trip_transport_end = $model->tripTransportEnd;
            if ($trip_transport_end != null) {
                $day_report_trip_transport_end = $trip_transport_end->dayReportTripTransport;
            }

            $suitcase_count_sent = 0;
            $bag_count_sent = 0;
            $oversized_count_sent = 0;
            if ($day_report_trip_transport_start != null) {
                $suitcase_count_sent += $day_report_trip_transport_start->suitcase_count_sent;
                $bag_count_sent += $day_report_trip_transport_start->bag_count_sent;
                $oversized_count_sent += $day_report_trip_transport_start->oversized_count_sent;
            }
            if ($day_report_trip_transport_end != null) {
                $suitcase_count_sent += $day_report_trip_transport_end->suitcase_count_sent;
                $bag_count_sent += $day_report_trip_transport_end->bag_count_sent;
                $oversized_count_sent += $day_report_trip_transport_end->oversized_count_sent;
            }

            return $suitcase_count_sent . 'Ч, ' . $bag_count_sent . 'С, ' . $oversized_count_sent . 'H';
        }
    ];
    $exportColumns[] = $column;
    $gridColumns[] = $column;

    $column = [
        'label' => 'Выр',//Всего Sum
        'content' => function ($model) {

            $day_report_trip_transport_start = null;
            $day_report_trip_transport_end = null;
            $trip_transport_start = $model->tripTransportStart;
            if ($trip_transport_start != null) {
                $day_report_trip_transport_start = $trip_transport_start->dayReportTripTransport;
            }
            $trip_transport_end = $model->tripTransportEnd;
            if ($trip_transport_end != null) {
                $day_report_trip_transport_end = $trip_transport_end->dayReportTripTransport;
            }

            $proceeds = 0;
            if ($day_report_trip_transport_start != null) {
                $proceeds += $day_report_trip_transport_start->proceeds;
            }
            if ($day_report_trip_transport_end != null) {
                $proceeds += $day_report_trip_transport_end->proceeds;
            }

            return $proceeds;
        }
    ];
    $exportColumns[] = $column;
    $gridColumns[] = $column;

    $exportColumns[] = [
        'label' => 'Б/з', // Значение поля "Без записи, руб." из блока "Корректировка"
        'content' => function ($model) {

            $day_report_trip_transport_start = null;
            $day_report_trip_transport_end = null;
            $trip_transport_start = $model->tripTransportStart;
            if ($trip_transport_start != null) {
                $day_report_trip_transport_start = $trip_transport_start->dayReportTripTransport;
            }
            $trip_transport_end = $model->tripTransportEnd;
            if ($trip_transport_end != null) {
                $day_report_trip_transport_end = $trip_transport_end->dayReportTripTransport;
            }

            $no_record = 0;
            if ($day_report_trip_transport_start != null) {
                $no_record += $day_report_trip_transport_start->no_record;
            }
            if ($day_report_trip_transport_end != null) {
                $no_record += $day_report_trip_transport_end->no_record;
            }

            return
                $no_record
                . (!empty($model->camera_no_record_comment) ? $model->camera_no_record_comment : '');
        },
        'filter' => false
    ];
    $gridColumns[] = [
        'label' => 'Б/з', // Значение поля "Без записи, руб." из блока "Корректировка"
        'content' => function ($model) {

            $day_report_trip_transport_start = null;
            $day_report_trip_transport_end = null;
            $trip_transport_start = $model->tripTransportStart;
            if ($trip_transport_start != null) {
                $day_report_trip_transport_start = $trip_transport_start->dayReportTripTransport;
            }
            $trip_transport_end = $model->tripTransportEnd;
            if ($trip_transport_end != null) {
                $day_report_trip_transport_end = $trip_transport_end->dayReportTripTransport;
            }

            $no_record = 0;
            if ($day_report_trip_transport_start != null) {
                $no_record += $day_report_trip_transport_start->no_record;
            }
            if ($day_report_trip_transport_end != null) {
                $no_record += $day_report_trip_transport_end->no_record;
            }

            return
                $no_record
                . (!empty($model->camera_no_record_comment) ? ' <span class="glyphicon glyphicon-share waybill-share-comment"></span><div class="waybill-comment">' . $model->camera_no_record_comment . '</div>' : '');
        }
    ];
}




$gridColumns[] = [
    'label' => 'Cт ЖД',//Сумма из Типового расхода "Стоянка"
    'content' => function ($model) use($stoyanka_expenses_seller_type) {
        $stoyanka_expense = null;
        $transport_expenses = $model->transportExpenses;
        foreach($transport_expenses as $tr_expenses) {
            if($tr_expenses->expenses_seller_type_id == $stoyanka_expenses_seller_type->id && $tr_expenses->view_group == 'typical_expenses') {
                $stoyanka_expense = $tr_expenses;
                break;
            }
        }

        if($stoyanka_expense != null) {
            if ($stoyanka_expense->expenses_is_taken != true) {
                return '<span style="color: #FF0000;">' . $stoyanka_expense->price . '</span>';
            } else {
                return $stoyanka_expense->price;
            }
        }else {
            return '-';
        }
    }
];
$exportColumns[] = [
    'label' => 'Cт ЖД',//Сумма из Типового расхода "Стоянка"
    'content' => function ($model) use($stoyanka_expenses_seller_type) {
        $stoyanka_expense = null;
        $transport_expenses = $model->transportExpenses;
        foreach($transport_expenses as $tr_expenses) {
            if($tr_expenses->expenses_seller_type_id == $stoyanka_expenses_seller_type->id && $tr_expenses->view_group == 'typical_expenses') {
                $stoyanka_expense = $tr_expenses;
                break;
            }
        }

        if($stoyanka_expense != null) {
            if ($stoyanka_expense->expenses_is_taken != true) {
                return $stoyanka_expense->price;
            } else {
                return $stoyanka_expense->price;
            }
        }else {
            return '-';
        }
    },
    'filter' => false
];


$column = [
    'label' => 'АЗС', // Наименование продавца из Типового расхода "АЗС"
    'content' => function ($model) use($azs_expenses_seller_type) {

        $azs_tr_expenses = 0;
        $transport_expenses = $model->transportExpenses;
        foreach($transport_expenses as $tr_expenses) {
            if($tr_expenses->expenses_seller_type_id == $azs_expenses_seller_type->id && $tr_expenses->view_group == 'typical_expenses') {
                $azs_tr_expenses = $tr_expenses;
                break;
            }
        }

        if($azs_tr_expenses != null && $azs_tr_expenses->expenses_seller_id > 0) {
            return $azs_tr_expenses->seller->name;
        }else {
            return '';
        }
    }
];
$exportColumns[] = $column;
$gridColumns[] = $column;


$column = [
    'label' => 'Баллы',// Баллы из Типового расхода "АЗС"
    'content' => function ($model) use($azs_expenses_seller_type) {

        $azs_tr_expenses = 0;
        $transport_expenses = $model->transportExpenses;
        foreach($transport_expenses as $tr_expenses) {
            if($tr_expenses->expenses_seller_type_id == $azs_expenses_seller_type->id && $tr_expenses->view_group == 'typical_expenses') {
                $azs_tr_expenses = $tr_expenses;
                break;
            }
        }

        if($azs_tr_expenses != null) {
            return $azs_tr_expenses->points;
        }else {
            return 0;
        }
    }
];
$exportColumns[] = $column;
$gridColumns[] = $column;



$exportColumns[] = [
    'label' => 'К-во', // К-во из Типового расхода "АЗС"
    'content' => function ($model) use($azs_expenses_seller_type) {

        $azs_tr_expenses = 0;
        $transport_expenses = $model->transportExpenses;
        foreach($transport_expenses as $tr_expenses) {
            if($tr_expenses->expenses_seller_type_id == $azs_expenses_seller_type->id && $tr_expenses->view_group == 'typical_expenses') {
                $azs_tr_expenses = $tr_expenses;
                break;
            }
        }

        // Подсвечивать красным, если не отмечен Чек (check_attached) или РП (expenses_is_taken)
        if($azs_tr_expenses != null) {

            if($azs_tr_expenses->check_attached != true || $azs_tr_expenses->expenses_is_taken != true) {
                return $azs_tr_expenses->count;
            }else {
                return $azs_tr_expenses->count;
            }
        }else {
            return 0;
        }
    },
    'filter' => false
];
$gridColumns[] = [
    'label' => 'К-во', // К-во из Типового расхода "АЗС"
    'content' => function ($model) use($azs_expenses_seller_type) {

        $azs_tr_expenses = 0;
        $transport_expenses = $model->transportExpenses;
        foreach($transport_expenses as $tr_expenses) {
            if($tr_expenses->expenses_seller_type_id == $azs_expenses_seller_type->id && $tr_expenses->view_group == 'typical_expenses') {
                $azs_tr_expenses = $tr_expenses;
                break;
            }
        }

        // Подсвечивать красным, если не отмечен Чек (check_attached) или РП (expenses_is_taken)
        if($azs_tr_expenses != null) {

            if($azs_tr_expenses->check_attached != true || $azs_tr_expenses->expenses_is_taken != true) {
                return '<span style="color: #FF0000;">'.$azs_tr_expenses->count.'</span>';
            }else {
                return $azs_tr_expenses->count;
            }
        }else {
            return 0;
        }
    }
];


$column = [
    'label' => 'Цена 1 л', // Сумма из Типового расхода "АЗС", поделенная на к-во из Типового расхода АЗС
    'content' => function ($model) use($azs_expenses_seller_type) {

        $azs_tr_expenses = null;
        $transport_expenses = $model->transportExpenses;
        foreach($transport_expenses as $tr_expenses) {
            if($tr_expenses->expenses_seller_type_id == $azs_expenses_seller_type->id && $tr_expenses->view_group == 'typical_expenses') {
                $azs_tr_expenses = $tr_expenses;
                break;
            }
        }

        if($azs_tr_expenses != null && $azs_tr_expenses->count > 0) {
            return round($azs_tr_expenses->price/$azs_tr_expenses->count, 2);
        }else {
            return 0;
        }
    }
];
$exportColumns[] = $column;
$gridColumns[] = $column;


$exportColumns[] = [
    'label' => 'Сумма',
    'content' => function ($model) use($azs_expenses_seller_type) {

        $azs_tr_expenses = null;
        $transport_expenses = $model->transportExpenses;
        foreach($transport_expenses as $tr_expenses) {
            if($tr_expenses->expenses_seller_type_id == $azs_expenses_seller_type->id && $tr_expenses->view_group == 'typical_expenses') {
                $azs_tr_expenses = $tr_expenses;
                break;
            }
        }

        if($azs_tr_expenses != null) {
            return
                $azs_tr_expenses->price
                .(!empty($azs_tr_expenses->expenses_is_taken_comment) ? $azs_tr_expenses->expenses_is_taken_comment : '');
        }else {
            return 0;
        }
    },
    'filter' => false
];
$gridColumns[] = [
    'label' => 'Сумма',
    'content' => function ($model) use($azs_expenses_seller_type) {

        $azs_tr_expenses = null;
        $transport_expenses = $model->transportExpenses;
        foreach($transport_expenses as $tr_expenses) {
            if($tr_expenses->expenses_seller_type_id == $azs_expenses_seller_type->id && $tr_expenses->view_group == 'typical_expenses') {
                $azs_tr_expenses = $tr_expenses;
                break;
            }
        }

        if($azs_tr_expenses != null) {
            return
                $azs_tr_expenses->price
                .(!empty($azs_tr_expenses->expenses_is_taken_comment) ? ' <span class="glyphicon glyphicon-share waybill-share-comment"></span><div class="waybill-comment">'.$azs_tr_expenses->expenses_is_taken_comment.'</div>' : '');
        }else {
            return 0;
        }
    }
];


$column = [
    'attribute' => 'accruals_to_issue_for_trip',
    'label' => 'ЗП', // Значение поля "К выдаче за рейс" из блока Начисления
    'content' => function ($model) {
        return $model->accruals_to_issue_for_trip;
    }
];
$exportColumns[] = $column;
$gridColumns[] = $column;


$column = [
    'attribute' => 'accruals_given_to_hand',
    'label' => 'Ав-с', // Значение поля "Выдано за руки" из блока Начисления
    'content' => function ($model) {
        return $model->accruals_given_to_hand;
    }
];
$exportColumns[] = $column;
$gridColumns[] = $column;



$exportColumns[] = [
    'label' => 'ТО.Мойка',  // Сумма из Типового расхода "Мойка"
    'content' => function ($model) use($moika_expenses_seller_type) {

        $moika_tr_expenses = null;
        $transport_expenses = $model->transportExpenses;
        foreach($transport_expenses as $tr_expenses) {
            if($tr_expenses->expenses_seller_type_id == $moika_expenses_seller_type->id && $tr_expenses->view_group == 'typical_expenses') {
                $moika_tr_expenses = $tr_expenses;
                break;
            }
        }

        if($moika_tr_expenses != null) {
            if($moika_tr_expenses->check_attached != true || $moika_tr_expenses->expenses_is_taken != true) {
                return $moika_tr_expenses->price;
            }else {
                return $moika_tr_expenses->price;
            }
        }else {
            return 0;
        }
    },
    'filter' => false
];
$gridColumns[] = [
    'label' => 'ТО.Мойка',  // Сумма из Типового расхода "Мойка"
    'content' => function ($model) use($moika_expenses_seller_type) {

        $moika_tr_expenses = null;
        $transport_expenses = $model->transportExpenses;
        foreach($transport_expenses as $tr_expenses) {
            if($tr_expenses->expenses_seller_type_id == $moika_expenses_seller_type->id && $tr_expenses->view_group == 'typical_expenses') {
                $moika_tr_expenses = $tr_expenses;
                break;
            }
        }

        if($moika_tr_expenses != null) {
            if($moika_tr_expenses->check_attached != true || $moika_tr_expenses->expenses_is_taken != true) {
                return '<span style="color: #FF0000;">'.$moika_tr_expenses->price.'</span>';
            }else {
                return $moika_tr_expenses->price;
            }
        }else {
            return 0;
        }
    }
];



$gridColumns[] = [
    'label' => 'ТО из выр', // Сумма из всех расходов из таблицы "Прочие расходы из выручки"
    'content' => function ($model) {
        // other_expenses - группа расходов "Прочие расходы из выручки"
        $is_red = false;
        $other_expenses_price = 0;
        $transport_expenses = $model->transportExpenses;
        foreach($transport_expenses as $tr_expenses) {
            if($tr_expenses->view_group == 'other_expenses') {
                $other_expenses_price += $tr_expenses->price;

                if($tr_expenses->check_attached != true || $tr_expenses->expenses_is_taken != true) {
                    $is_red = true;
                }
            }
        }

        if($is_red == true) {
            return '<span style="color: #FF0000;">'.$other_expenses_price.'</span>';
        }else {
            return $other_expenses_price;
        }
    }
];
$exportColumns[] = [
    'label' => 'ТО из выр', // Сумма из всех расходов из таблицы "Прочие расходы из выручки"
    'content' => function ($model) {
        // other_expenses - группа расходов "Прочие расходы из выручки"
        $is_red = false;
        $other_expenses_price = 0;
        $transport_expenses = $model->transportExpenses;
        foreach($transport_expenses as $tr_expenses) {
            if($tr_expenses->view_group == 'other_expenses') {
                $other_expenses_price += $tr_expenses->price;

                if($tr_expenses->check_attached != true || $tr_expenses->expenses_is_taken != true) {
                    $is_red = true;
                }
            }
        }

        if($is_red == true) {
            return $other_expenses_price;
        }else {
            return $other_expenses_price;
        }
    },
    'filter' => false
];


$exportColumns[] = [

    'label' => 'ТО расш', // Отображает все расходы из таблицы "Прочие расходы из выручки" в формате:
    // 500-мойка; 1100-стоянка за месяц; 70-такси
    'content' => function ($model) {
        $aOtherExpensesRows = [];
        $aKrpComments = [];
        $transport_expenses = $model->transportExpenses;
        foreach($transport_expenses as $tr_expenses) {
            if($tr_expenses->view_group == 'other_expenses') {
                $aOtherExpensesRows[] = $tr_expenses->price.'-'.($tr_expenses->expenses_seller_type_id > 0 ? $tr_expenses->sellerType->name : '');
                if(!empty($tr_expenses->expenses_is_taken_comment)) {
                    $aKrpComments[] = $tr_expenses->expenses_is_taken_comment;
                }
            }
        }

        return
            implode('; ', $aOtherExpensesRows)
            .(count($aKrpComments) > 0 ? ' '.implode(' ', $aKrpComments) : '');
    },
    'filter' => false
];
$gridColumns[] = [

    'label' => 'ТО расш', // Отображает все расходы из таблицы "Прочие расходы из выручки" в формате:
    // 500-мойка; 1100-стоянка за месяц; 70-такси
    'content' => function ($model) {
        $aOtherExpensesRows = [];
        $aKrpComments = [];
        $transport_expenses = $model->transportExpenses;
        foreach($transport_expenses as $tr_expenses) {
            if($tr_expenses->view_group == 'other_expenses') {
                $aOtherExpensesRows[] = $tr_expenses->price.'-'.($tr_expenses->expenses_seller_type_id > 0 ? $tr_expenses->sellerType->name : '');
                if(!empty($tr_expenses->expenses_is_taken_comment)) {
                    $aKrpComments[] = $tr_expenses->expenses_is_taken_comment;
                }
            }
        }

        return
            implode('; ', $aOtherExpensesRows)
            .(count($aKrpComments) > 0 ? ' <span class="glyphicon glyphicon-share waybill-share-comment"></span><div class="waybill-comment">'.implode('<br /><br />', $aKrpComments).'</div>' : '');
    }
];


$gridColumns[] = [
    'label' => 'ТО вх', // Сумма из всех расходов из таблицы "Входящие требования оплаты" - incoming_payment_requests
    'content' => function ($model) {

        $is_red = false;
        $incoming_payment_expenses_price = 0;
        $transport_expenses = $model->transportExpenses;
        foreach($transport_expenses as $tr_expenses) {
            if($tr_expenses->view_group == 'incoming_payment_requests') {
                $incoming_payment_expenses_price += $tr_expenses->price;

                if($tr_expenses->check_attached != true) {
                    $is_red = true;
                }
            }
        }

        if($is_red == true) {
            return '<span style="color: #FF0000;">'.$incoming_payment_expenses_price.'</span>';
        }else {
            return $incoming_payment_expenses_price;
        }
    }
];
$exportColumns[] = [
    'label' => 'ТО вх', // Сумма из всех расходов из таблицы "Входящие требования оплаты" - incoming_payment_requests
    'content' => function ($model) {

        $is_red = false;
        $incoming_payment_expenses_price = 0;
        $transport_expenses = $model->transportExpenses;
        foreach($transport_expenses as $tr_expenses) {
            if($tr_expenses->view_group == 'incoming_payment_requests') {
                $incoming_payment_expenses_price += $tr_expenses->price;

                if($tr_expenses->check_attached != true) {
                    $is_red = true;
                }
            }
        }

        if($is_red == true) {
            return $incoming_payment_expenses_price;
        }else {
            return $incoming_payment_expenses_price;
        }
    },
    'filter' => false
];


$gridColumns[] = [
    'attribute' => 'tovxrash',
    'label' => 'ТО вх расш',
    'content' => function ($model) {
        return $model->getTovxrash();
    },
    'filter' => SelectWidget::widget([
        'model' => $searchModel,
        'attribute' => 'tovxrash',
        //'name' => 'driver_id',
        'initValueText' => ($searchModel->getTovxrash() > 0 ? TransportWaybill::find()->where(['id' => $searchModel->getTovxrash()])->one()->getShortTovxrash() : ''),
        //'initValueText' => ($searchModel->tovxrash > 0 ? $searchModel->getShortTovxrash().'sdf' : ''),
        'options' => [
            'placeholder' => 'Введите название...',
        ],
        'ajax' => [
            'url' => '/waybill/transport-waybill/ajax-get-tovxrash',
            'data' => new JsExpression('function(params, $obj) {
                return {
                    search: params.search
                };
            }'),
        ],
        'using_delete_button' => true
    ])
];
$exportColumns[] = [
    'attribute' => 'tovxrash',
    'label' => 'ТО вх расш',
    'content' => function ($model) {
        return $model->getTovxrash(false);
    },
    'filter' => false
];


if(!in_array(Yii::$app->session->get('role_alias'), ['warehouse_turnover'])) { // Эксплуатация не видит этих колонок

    $column = [
        'attribute' => 'hand_over_b1',
        'label' => 'Сдано В1',
        'content' => function ($model) {
            return $model->hand_over_b1;
        }
    ];
    $exportColumns[] = $column;
    $gridColumns[] = $column;


    $column = [
        'attribute' => 'hand_over_b1_data',
        'label' => 'Дата В1',
        'content' => function ($model) {
            return ($model->hand_over_b1_data > 0 ? date('d.m.Y', $model->hand_over_b1_data) : '');
        }
    ];
    $exportColumns[] = $column;
    $gridColumns[] = $column;


    $column = [
        'attribute' => 'hand_over_b2',
        'label' => 'Сдано В2',
        'content' => function ($model) {
            return $model->hand_over_b2;
        }
    ];
    $exportColumns[] = $column;
    $gridColumns[] = $column;


    $column = [
        'attribute' => 'hand_over_b2_data',
        'label' => 'Дата В2',
        'content' => function ($model) {
            return ($model->hand_over_b2_data > 0 ? date('d.m.Y', $model->hand_over_b2_data) : '');
        }
    ];
    $exportColumns[] = $column;
    $gridColumns[] = $column;
}

$column = [
    'attribute' => 'total_failure_to_pay',
    'content' => function ($model) {
        return $model->total_failure_to_pay;
    }
];
$exportColumns[] = $column;
$gridColumns[] = $column;


if(in_array(Yii::$app->session->get('role_alias'), ['warehouse_turnover'])) { // Эксплуатация не видит этих колонок
    $column = [
        'attribute' => 'camera_eduction',
        'label' => 'Вычет',
        'content' => function ($model) {
            return $model->camera_eduction;
        }
    ];
    $exportColumns[] = $column;
    $gridColumns[] = $column;
}

if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin'])) {
    $column = [
        'attribute' => 'total_net_profit',
        'label' => 'ЧППЛ',
        'content' => function ($model) {
            return $model->total_net_profit;
        }
    ];
    $exportColumns[] = $column;
    $gridColumns[] = $column;
}

if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin'])) {

    $column = [
        'attribute' => 'created_at',
        'content' => function ($model) {
            return date('d.m.Y', $model->created_at);
        }
    ];
    $exportColumns[] = $column;
    $gridColumns[] = $column;


    $column = [
        'attribute' => 'creator_id',
        'content' => function ($model) {
            return ($model->creator_id > 0 ? $model->creator->lastname . ' ' . $model->creator->firstname : '');
        }
    ];
    $exportColumns[] = $column;
    $gridColumns[] = $column;


    $column = [
        'attribute' => 'changes_history',
        'content' => function ($model) {
            return $model->changes_history;
        }
    ];
    $exportColumns[] = $column;
    $gridColumns[] = $column;
}


//    'pre_trip_med_check',
//    'pre_trip_med_check_time',
//    'pre_trip_tech_check',
//    'pre_trip_tech_check_time',
//    'after_trip_med_check',
//    'after_trip_med_check_time',
//    'after_trip_tech_check',
//    'after_trip_tech_check_time',
//    'mileage_before_departure',
//    'mileage_after_departure',
//    'departure_time',
//    'return_time',
//    'trip_transport_start',
//    'trip_transport_end',

?>

<?= $this->render('_menu') ?>


<h2><?= Html::encode($this->title) ?></h2>
<div id="transport-waybill-index" class="box box-default">

    <div class="box-header scroller with-border">
        <div class="pull-left">
            <?php
            //echo Html::a('<i class="glyphicon glyphicon-plus"></i> Создать путевой лист', ['create'], ['class' => 'btn btn-success']);
            ?>
        </div>


        <br />
        <div class="pull-left">
            <?php
//            $exportColumns = [
//                ['class' => 'kartik\grid\SerialColumn'],
//            ];
//            $exportColumns = array_merge($exportColumns, $columns);

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
        if(empty(Yii::$app->session->get('table-rows'))) {
            Yii::$app->session->set('table-rows', 20);
        }

        echo (new PageSizeHelper([10, 20, 50, 100, 200, 500]))->getButtons();
        ?>
    </div>

    <br /><br />
    <div class="box-body box-table" style="clear: both;">

        <?php
//        $gridColumns = [];
//        $gridColumns[] =
//            [
//                'content' => function ($model, $key, $index) {
//                    return $key;
//                },
//                'header' => '#',
//            ];
//
//
//        $gridColumns[] =
//            [
//                'class' => 'yii\grid\ActionColumn',
//                'template' => '{update}',
//                'options' => ['style' => 'width: 25px;'],
//                'buttons' => [
//                    'update' => function ($url, $model)
//                    {
//                        return Html::a(
//                            '<span class="glyphicon glyphicon-pencil"></span>',
//                            Url::to(['/waybill/transport-waybill/update', 'id' => $model->id]),
//                            [
//                                'title' => 'Редактировать',
//                                'aria-label' => 'Редактировать'
//                            ]
//                        );
//                    },
//                ],
//            ];


        //$gridColumns = array_merge($gridColumns, $columns);


        echo GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'layout' => "{summary}\n<div class='list-data-table-over'><div class='list-data-table-inner'>{items}</div></div>",
            'options' => [
                'class' => 'grid-view table-responsive',
            ],
            'tableOptions' => [
                'class' => 'table table-striped table-bordered',
                'id' => 'list-data-table'
            ],
//            'rowOptions' => function ($model, $key, $index, $grid) {
//                $style = '';
//                if(empty($model->payment_date)) {
//                    $style = 'background-color: #FFFF00;';
//                }
//
//                return [
//                    'style' => $style,
//                ];
//            },
            'columns' => $gridColumns,
            'showFooter' => true,
        ]);
        ?>
    </div>
</div>
