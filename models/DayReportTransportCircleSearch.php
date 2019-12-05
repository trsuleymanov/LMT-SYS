<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\DayReportTransportCircle;

/**
 * DayReportTransportCircleSearch represents the model behind the search form about `app\models\DayReportTransportCircle`.
 */
class DayReportTransportCircleSearch extends DayReportTransportCircle
{
    public $event = 1;

    public static function getGridColumns($dataProvider, $date) {

        $columns = [
            ['class' => 'yii\grid\SerialColumn'],

//        [
//            'attribute' => 'event',
//            'content' => function($model) use($date) {
//                return $model->getEvent($date);
//            }
//        ],
            [
                'attribute' => 'direction_name_1',
                'label' => 'НП',
                'content' => function($model) use($date) {

                    if($model->baseCityDayReport != null) {

                        if($model->getEvent($date) == 5) {
                            return '<p class="text-danger">'.$model->baseCityDayReport->direction_name.'</p>';
                        }else {
                            return $model->baseCityDayReport->direction_name;
                        }
                    }else {
                        return '';
                    }
                },
                'footer' => ''
            ],
            [
                'attribute' => 'trip_name_1',
                'label' => 'Рейс',
                'content' => function($model) use($date) {

                    if($model->getEvent($date) == 4) {
                        return '<p class="text-danger">Первый запуск машины</p>';
                    }else {
                        if ($model->baseCityDayReport != null) {

                            if($model->getEvent($date) == 5) {
                                return
                                    '<p class="text-danger">'
                                    . $model->baseCityDayReport->trip_name.'<br />'
                                    . (!empty($model->baseCityDayReport->trip_date_sended) ? '<br />' . date("H:i d.m.Y", $model->baseCityDayReport->trip_date_sended) : '')
                                    . (!empty($model->baseCityDayReport->trip_sender_fio) ? '<br />' . $model->baseCityDayReport->trip_sender_fio : '')
                                    .'</p>';
                            }else {
                                return
                                    $model->baseCityDayReport->trip_name.'<br />'
                                    . (!empty($model->baseCityDayReport->trip_date_sended) ? '<br />' . date("H:i d.m.Y", $model->baseCityDayReport->trip_date_sended) : '')
                                    . (!empty($model->baseCityDayReport->trip_sender_fio) ? '<br />' . $model->baseCityDayReport->trip_sender_fio : '');
                            }
                        } else {
                            return '';
                        }
                    }
                },
                'footer' => ''
            ],
            [
                'attribute' => 'transport_car_reg_1',
                'label' => 'Т/с',
                'content' => function($model) use($date) {
                    if($model->baseCityDayReport != null && !empty($model->baseCityDayReport->transport_car_reg)) {

                        if($model->getEvent($date) == 5) {
                            return
                                '<p class="text-danger">'
                                . $model->baseCityDayReport->transport_car_reg . ' ' . $model->baseCityDayReport->transport_model . ' (' . $model->baseCityDayReport->transport_places_count . ')'
                                . '<br />' . $model->baseCityDayReport->driver_fio
                                . (!empty($model->baseCityDayReport->transport_date_sended) ? '<br />Т/с отпр. ' . date("H:i d.m.Y", $model->baseCityDayReport->transport_date_sended) . ',<br />' . $model->baseCityDayReport->transport_sender_fio : '')
                                .'</p>';
                        }else {
                            return
                                $model->baseCityDayReport->transport_car_reg . ' ' . $model->baseCityDayReport->transport_model . ' (' . $model->baseCityDayReport->transport_places_count . ')'
                                . '<br />' . $model->baseCityDayReport->driver_fio
                                . (!empty($model->baseCityDayReport->transport_date_sended) ? '<br />Т/с отпр. ' . date("H:i d.m.Y", $model->baseCityDayReport->transport_date_sended) . ',<br />' . $model->baseCityDayReport->transport_sender_fio : '');
                        }

                    }else {
                        return '';
                    }
                },
                'footer' => ''
            ],
            [
                'attribute' => 'places_count_sent_1',
                'label' => 'М', //Всего
                'content' => function($model) use($date) {
                    if($model->baseCityDayReport != null && $model->baseCityDayReport->places_count_sent != null) {

                        if($model->getEvent($date) == 5) {
                            return '<p class="text-danger">'.$model->baseCityDayReport->places_count_sent.'</p>';
                        }else {
                            return $model->baseCityDayReport->places_count_sent;
                        }
                    }else {
                        return '';
                    }
                },
                'footer' => DayReportTransportCircleSearch::getSumm($dataProvider->models, 'places_count_sent_1')
            ],
            [
                'attribute' => 'child_count_sent_1',
                'label' => 'Д',
                'content' => function($model) use($date) {
                    if($model->baseCityDayReport != null && $model->baseCityDayReport->child_count_sent != null) {

                        if($model->getEvent($date) == 5) {
                            return '<p class="text-danger">'.$model->baseCityDayReport->child_count_sent.'</p>';
                        }else {
                            return $model->baseCityDayReport->child_count_sent;
                        }
                    }else {
                        return '';
                    }
                },
                'footer' => DayReportTransportCircleSearch::getSumm($dataProvider->models, 'child_count_sent_1')
            ],
            [
                'attribute' => 'student_count_sent_1',
                'label' => 'С',
                'content' => function($model) use($date) {
                    if($model->baseCityDayReport != null && $model->baseCityDayReport->student_count_sent != null) {

                        if($model->getEvent($date) == 5) {
                            return '<p class="text-danger">'.$model->baseCityDayReport->student_count_sent.'</p>';
                        }else {
                            return $model->baseCityDayReport->student_count_sent;
                        }
                    }else {
                        return '';
                    }
                },
                'footer' => DayReportTransportCircleSearch::getSumm($dataProvider->models, 'student_count_sent_1')
            ],
//            [
//                'attribute' => 'airport_count_sent_1',
//                'label' => 'А',
//                'content' => function($model) use($date) {
//                    if($model->baseCityDayReport != null && $model->baseCityDayReport->airport_count_sent != null) {
//
//                        if($model->getEvent($date) == 5) {
//                            return '<p class="text-danger">'.$model->baseCityDayReport->airport_count_sent.'</p>';
//                        }else {
//                            return $model->baseCityDayReport->airport_count_sent;
//                        }
//
//                    }else {
//                        return '';
//                    }
//                },
//                'footer' => DayReportTransportCircleSearch::getSumm($dataProvider->models, 'airport_count_sent_1')
//            ],
            [
                'attribute' => 'airport_places_count_sent_1',
                'label' => 'А',
                'content' => function($model) use($date) {
                    if($model->baseCityDayReport != null && $model->baseCityDayReport->airport_places_count_sent != null) {

                        if($model->getEvent($date) == 5) {
                            return '<p class="text-danger">'.$model->baseCityDayReport->airport_places_count_sent.'</p>';
                        }else {
                            return $model->baseCityDayReport->airport_places_count_sent;
                        }

                    }else {
                        return '';
                    }
                },
                'footer' => DayReportTransportCircleSearch::getSumm($dataProvider->models, 'airport_places_count_sent_1')
            ],
            [
                'attribute' => 'prize_trip_count_sent_1',
                'label' => 'П',
                'content' => function($model) use($date) {
                    if($model->baseCityDayReport != null && $model->baseCityDayReport->prize_trip_count_sent != null) {

                        if($model->getEvent($date) == 5) {
                            return '<p class="text-danger">'.$model->baseCityDayReport->prize_trip_count_sent.'</p>';
                        }else {
                            return $model->baseCityDayReport->prize_trip_count_sent;
                        }

                    }else {
                        return '';
                    }
                },
                'footer' => DayReportTransportCircleSearch::getSumm($dataProvider->models, 'prize_trip_count_sent_1')
            ],
            [
                'attribute' => 'is_not_places_count_sent_1',
                'label' => 'БМ', //Без места
                'content' => function($model) use($date) {

                    if($model->baseCityDayReport != null && $model->baseCityDayReport->is_not_places_count_sent != null) {

                        if($model->getEvent($date) == 5) {
                            return '<p class="text-danger">'.$model->baseCityDayReport->is_not_places_count_sent.'</p>';
                        }else {
                            return $model->baseCityDayReport->is_not_places_count_sent;
                        }

                    }else {
                        return '';
                    }
                },
                'footer' => DayReportTransportCircleSearch::getSumm($dataProvider->models, 'is_not_places_count_sent_1')
            ],
            [
                'attribute' => 'bag_count_sent_1',
                'label' => 'Lug',
                'content' => function($model) use($date) {

                    if($model->baseCityDayReport != null) {
                        if($model->baseCityDayReport->suitcase_count_sent == null && $model->baseCityDayReport->bag_count_sent == null && $model->baseCityDayReport->oversized_count_sent == null) {
                            return '';
                        }else {

                            if($model->getEvent($date) == 5) {
                                return '<p class="text-danger">'.$model->baseCityDayReport->suitcase_count_sent . 'Ч, ' . $model->baseCityDayReport->bag_count_sent . 'С, ' . $model->baseCityDayReport->oversized_count_sent . 'Н'.'</p>';
                            }else {
                                return $model->baseCityDayReport->suitcase_count_sent . 'Ч, ' . $model->baseCityDayReport->bag_count_sent . 'С, ' . $model->baseCityDayReport->oversized_count_sent . 'Н';
                            }
                        }
                    }else {
                        return '';
                    }
                },
                'footer' => DayReportTransportCircleSearch::getSumm($dataProvider->models, 'suitcase_count_sent_1') . 'Ч, ' . DayReportTransportCircleSearch::getSumm($dataProvider->models, 'bag_count_sent_1') . 'С, ' . DayReportTransportCircleSearch::getSumm($dataProvider->models, 'oversized_count_sent_1') . 'Н'
            ],
            [
                'attribute' => 'proceeds_1',
                //'label' => 'Sum<br />Paid',
                'header' => 'Sum/<br />Paid',
                'content' => function($model) use($date) {

                    if($model->baseCityDayReport != null && $model->baseCityDayReport->proceeds != null) {

                        if($model->getEvent($date) == 5) {
                            return '<p class="text-danger">'.intval($model->baseCityDayReport->proceeds).'/<br />'.$model->baseCityDayReport->paid_summ.'</p>';
                        }else {
                            //return number_format(intval($model->baseCityDayReport->proceeds), 0, '', ' ');
                            return intval($model->baseCityDayReport->proceeds).'/<br />'.$model->baseCityDayReport->paid_summ;
                        }

                    }else {
                        return '';
                    }
                },
                //'footer' => number_format(intval(DayReportTransportCircleSearch::getSumm($dataProvider->models, 'proceeds_1')), 0, '', ' '),
                'footer' => intval(DayReportTransportCircleSearch::getSumm($dataProvider->models, 'proceeds_1')),
            ],


            [
                'attribute' => 'direction_name_2',
                'label' => 'НП',
                'content' => function($model) use($date) {
                    if (in_array($model->getEvent($date), [1, 2, 3])) {
                        return '';
                    } else {
                        if ($model->notbaseCityDayReport != null) {
                            return $model->notbaseCityDayReport->direction_name;
                        } else {
                            return '';
                        }
                    }
                },
                'footer' => ''
            ],

            [
                'attribute' => 'trip_name_2',
                'label' => 'Рейс',
                'content' => function($model) use($date) {

                    if($model->getEvent($date) == 1) {
                        return '<p class="text-danger">В ожидании обратного рейса...</p>';
                    }elseif($model->getEvent($date) == 2) {
                        //return '<p class="text-danger">Без загрузки</p>';
                        return '<p class="text-danger">Круг завершен '.(!empty($model->time_setting_state) ? date("H:i d.m.Y", $model->time_setting_state) : '').'</p>';
                    }elseif($model->getEvent($date) == 3) {
//                        return '<p class="text-danger">'
//                        . $model->notbaseCityDayReport->trip_name.'<br />'
//                        . (!empty($model->notbaseCityDayReport->trip_date_sended) ? '<br />' . date("H:i d.m.Y", $model->notbaseCityDayReport->trip_date_sended) : '')
//                        . (!empty($model->notbaseCityDayReport->trip_sender_fio) ? '<br />' . $model->notbaseCityDayReport->trip_sender_fio : '')
//                        . '</p>';
                        return '<p class="text-danger">Круг завершен '.(!empty($model->time_setting_state) ? date("H:i d.m.Y", $model->time_setting_state) : '').'</p>';
                    }else {

                        if ($model->notbaseCityDayReport != null) {
                            return
                                $model->notbaseCityDayReport->trip_name.'<br />'
                                . (!empty($model->notbaseCityDayReport->trip_date_sended) ? '<br />' . date("H:i d.m.Y", $model->notbaseCityDayReport->trip_date_sended) : '')
                                . (!empty($model->notbaseCityDayReport->trip_sender_fio) ? '<br />' . $model->notbaseCityDayReport->trip_sender_fio : '');
                        } else {
                            return '';
                        }
                    }
                },
                'footer' => ''
            ],
            [
                'attribute' => 'transport_car_reg_2',
                'label' => 'Т/с',
                'content' => function($model) use($date) {

                    if (in_array($model->getEvent($date), [1])) {
                        return '';
                    } elseif(in_array($model->getEvent($date), [2, 3])) {
                        return '-';
                    } else {
                        if ($model->notbaseCityDayReport != null && !empty($model->notbaseCityDayReport->transport_car_reg)) {
                            return
                                $model->notbaseCityDayReport->transport_car_reg . ' ' . $model->notbaseCityDayReport->transport_model . ' (' . $model->notbaseCityDayReport->transport_places_count . ')'
                                . '<br />' . $model->notbaseCityDayReport->driver_fio
                                . (!empty($model->notbaseCityDayReport->transport_date_sended) ? '<br />Т/с отпр. ' . date("H:i d.m.Y", $model->notbaseCityDayReport->transport_date_sended) . ',<br />' . $model->notbaseCityDayReport->transport_sender_fio : '');
                        } else {
                            return '';
                        }
                    }
                },
                'footer' => ''
            ],


            [
                'attribute' => 'places_count_sent_2',
                'label' => 'М', //Всего
                'content' => function($model) use($date) {

                    if (in_array($model->getEvent($date), [1, 3])) {
                        return '';
                    } elseif(in_array($model->getEvent($date), [2, 3])) {
                        return '-';
                    } else {
                        if ($model->notbaseCityDayReport != null && $model->notbaseCityDayReport->places_count_sent != null) {
                            return $model->notbaseCityDayReport->places_count_sent;
                        } else {
                            return '';
                        }
                    }
                },
                'footer' => DayReportTransportCircleSearch::getSumm($dataProvider->models, 'places_count_sent_2')
            ],
            [
                'attribute' => 'child_count_sent_2',
                'label' => 'Д',
                'content' => function($model) use($date) {

                    if (in_array($model->getEvent($date), [1])) {
                        return '';
                    } elseif(in_array($model->getEvent($date), [2, 3])) {
                        return '-';
                    } else {
                        if ($model->notbaseCityDayReport != null && $model->notbaseCityDayReport->child_count_sent != null) {
                            return $model->notbaseCityDayReport->child_count_sent;
                        } else {
                            return '';
                        }
                    }
                },
                'footer' => DayReportTransportCircleSearch::getSumm($dataProvider->models, 'child_count_sent_2')
            ],
            [
                'attribute' => 'student_count_sent_2',
                'label' => 'С',
                'content' => function($model) use($date) {

                    if (in_array($model->getEvent($date), [1])) {
                        return '';
                    } elseif(in_array($model->getEvent($date), [2, 3])) {
                        return '-';
                    } else {
                        if ($model->notbaseCityDayReport != null && $model->notbaseCityDayReport->student_count_sent != null) {
                            return $model->notbaseCityDayReport->student_count_sent;
                        } else {
                            return '';
                        }
                    }
                },
                'footer' => DayReportTransportCircleSearch::getSumm($dataProvider->models, 'student_count_sent_2')
            ],
//            [
//                'attribute' => 'airport_count_sent_2',
//                'label' => 'А',
//                'content' => function($model) use($date) {
//
//                    if (in_array($model->getEvent($date), [1])) {
//                        return '';
//                    } elseif(in_array($model->getEvent($date), [2, 3])) {
//                        return '-';
//                    } else {
//                        if ($model->notbaseCityDayReport != null && $model->notbaseCityDayReport->airport_count_sent != null) {
//                            return $model->notbaseCityDayReport->airport_count_sent;
//                        } else {
//                            return '';
//                        }
//                    }
//                },
//                'footer' => DayReportTransportCircleSearch::getSumm($dataProvider->models, 'airport_count_sent_2')
//            ],
            [
                'attribute' => 'airport_places_count_sent_2',
                'label' => 'А',
                'content' => function($model) use($date) {

                    if (in_array($model->getEvent($date), [1])) {
                        return '';
                    } elseif(in_array($model->getEvent($date), [2, 3])) {
                        return '-';
                    } else {
                        if ($model->notbaseCityDayReport != null && $model->notbaseCityDayReport->airport_places_count_sent != null) {
                            return $model->notbaseCityDayReport->airport_places_count_sent;
                        } else {
                            return '';
                        }
                    }
                },
                'footer' => DayReportTransportCircleSearch::getSumm($dataProvider->models, 'airport_places_count_sent_2')
            ],
            [
                'attribute' => 'prize_trip_count_sent_2',
                'label' => 'П',
                'content' => function($model) use($date) {

                    if (in_array($model->getEvent($date), [1])) {
                        return '';
                    } elseif(in_array($model->getEvent($date), [2, 3])) {
                        return '-';
                    } else {
                        if ($model->notbaseCityDayReport != null && $model->notbaseCityDayReport->prize_trip_count_sent != null) {
                            return $model->notbaseCityDayReport->prize_trip_count_sent;
                        } else {
                            return '';
                        }
                    }
                },
                'footer' => DayReportTransportCircleSearch::getSumm($dataProvider->models, 'prize_trip_count_sent_2')
            ],
            [
                'attribute' => 'is_not_places_count_sent_2',
                'label' => 'БМ', //Без места
                'content' => function($model) use($date) {

                    if (in_array($model->getEvent($date), [1])) {
                        return '';
                    } elseif(in_array($model->getEvent($date), [2, 3])) {
                        return '-';
                    } else {
                        if ($model->notbaseCityDayReport != null && $model->notbaseCityDayReport->is_not_places_count_sent != null) {
                            return $model->notbaseCityDayReport->is_not_places_count_sent;
                        } else {
                            return '';
                        }
                    }
                },
                'footer' => DayReportTransportCircleSearch::getSumm($dataProvider->models, 'is_not_places_count_sent_2')
            ],
            [
                'attribute' => 'bag_count_sent_2',
                'label' => 'Lug',
                'content' => function($model) use($date) {

                    if (in_array($model->getEvent($date), [1])) {
                        return '';
                    } elseif(in_array($model->getEvent($date), [2, 3])) {
                        return '-';
                    } else {
                        if ($model->notbaseCityDayReport != null) {
                            if ($model->notbaseCityDayReport->suitcase_count_sent == null && $model->notbaseCityDayReport->bag_count_sent == null && $model->notbaseCityDayReport->oversized_count_sent == null) {
                                return '';
                            } else {
                                return $model->notbaseCityDayReport->suitcase_count_sent . 'Ч, ' . $model->notbaseCityDayReport->bag_count_sent . 'С, ' . $model->notbaseCityDayReport->oversized_count_sent . 'Н';
                            }
                        } else {
                            return '';
                        }
                    }
                },
                'footer' => DayReportTransportCircleSearch::getSumm($dataProvider->models, 'suitcase_count_sent_2') . 'Ч, ' . DayReportTransportCircleSearch::getSumm($dataProvider->models, 'bag_count_sent_2') . 'С, ' . DayReportTransportCircleSearch::getSumm($dataProvider->models, 'oversized_count_sent_2') . 'Н'
            ],
            [
                'attribute' => 'proceeds_2',
                //'label' => 'Sum<br />Paid',
                'header' => 'Sum/<br />Paid',
                'content' => function($model) use($date) {

                    if (in_array($model->getEvent($date), [1])) {
                        return '';
                    } elseif(in_array($model->getEvent($date), [2])) {
                        return '0';
                    } elseif(in_array($model->getEvent($date), [3])) {
                        return '-';
                    } else {
                        if ($model->notbaseCityDayReport != null && $model->notbaseCityDayReport->proceeds != null) {
                            return intval($model->notbaseCityDayReport->proceeds).'/<br />'.$model->notbaseCityDayReport->paid_summ;
                        } else {
                            return '';
                        }
                    }
                },
                //'footer' => number_format(intval(DayReportTransportCircleSearch::getSumm($dataProvider->models, 'proceeds_2')), 0, '', ' '),
                'footer' => intval(DayReportTransportCircleSearch::getSumm($dataProvider->models, 'proceeds_2')),
            ],

            [
                'attribute' => 'total_proceeds',
                //'label' => 'Круг/Оплачено',
                'header' => 'Круг/<br />Оплачено',
                'content' => function($model) use($date) {
                    if (in_array($model->getEvent($date), [3])) {
                        return '-';
                    } else {
                        //return number_format(intval($model->total_proceeds), 0, '', ' ');
                        return intval($model->total_proceeds).'/<br />'.$model->total_paid_summ;
                    }
                },
                //'footer' => number_format(intval(DayReportTransportCircleSearch::getSumm($dataProvider->models, 'total_proceeds')), 0, '', ' '),
                'footer' => intval(DayReportTransportCircleSearch::getSumm($dataProvider->models, 'total_proceeds')),
            ],

            [
                'attribute' => 'formula_percent',
                'label' => '%',
                'content' => function($model) use($date) {

                    if (in_array($model->getEvent($date), [3])) {
                        return '-';
                    } else {
                        if ($model->transport != null && $model->transport->formula != null) {
                            return intval($model->transport->formula->getResult($model->total_proceeds));
                        } else {
                            return '';
                        }
                    }

                },
                'footer' => intval(DayReportTransportCircleSearch::getSumm($dataProvider->models, 'formula_percent')),
            ]
        ];

        return $columns;
    }

    public static function getSumm($models, $field_name) {

        $summ = 0;
        foreach($models as $model) {


            switch($field_name) {
                case 'places_count_sent_1':
                    if($model->baseCityDayReport != null && $model->baseCityDayReport->places_count_sent != null) {
                        $summ += $model->baseCityDayReport->places_count_sent;
                    }
                    break;
                case 'child_count_sent_1':
                    if($model->baseCityDayReport != null && $model->baseCityDayReport->child_count_sent != null) {
                        $summ += $model->baseCityDayReport->child_count_sent;
                    }
                    break;

                case 'student_count_sent_1':
                    if($model->baseCityDayReport != null && $model->baseCityDayReport->student_count_sent != null) {
                        $summ += $model->baseCityDayReport->student_count_sent;
                    }
                    break;

                case 'prize_trip_count_sent_1':
                    if($model->baseCityDayReport != null && $model->baseCityDayReport->prize_trip_count_sent != null) {
                        $summ += $model->baseCityDayReport->prize_trip_count_sent;
                    }
                    break;

                case 'suitcase_count_sent_1':
                    if($model->baseCityDayReport != null && $model->baseCityDayReport->suitcase_count_sent != null) {
                        $summ += $model->baseCityDayReport->suitcase_count_sent;
                    }
                    break;

                case 'bag_count_sent_1':
                    if($model->baseCityDayReport != null && $model->baseCityDayReport->bag_count_sent != null) {
                        $summ += $model->baseCityDayReport->bag_count_sent;
                    }
                    break;

                case 'oversized_count_sent_1':
                    if($model->baseCityDayReport != null && $model->baseCityDayReport->oversized_count_sent != null) {
                        $summ += $model->baseCityDayReport->oversized_count_sent;
                    }
                    break;

                case 'is_not_places_count_sent_1':
                    if($model->baseCityDayReport != null && $model->baseCityDayReport->is_not_places_count_sent != null) {
                        $summ += $model->baseCityDayReport->is_not_places_count_sent;
                    }
                    break;
//                case 'airport_count_sent_1':
//                    if($model->baseCityDayReport != null && $model->baseCityDayReport->airport_count_sent != null) {
//                        $summ += $model->baseCityDayReport->airport_count_sent;
//                    }
//                    break;
                case 'airport_places_count_sent_1':
                    if($model->baseCityDayReport != null && $model->baseCityDayReport->airport_places_count_sent != null) {
                        $summ += $model->baseCityDayReport->airport_places_count_sent;
                    }
                    break;
                case 'proceeds_1':
                    if($model->baseCityDayReport != null && $model->baseCityDayReport->proceeds != null) {
                        $summ += $model->baseCityDayReport->proceeds;
                    }
                    break;



                case 'places_count_sent_2':
                    if($model->notbaseCityDayReport != null && $model->notbaseCityDayReport->places_count_sent != null) {
                        $summ += $model->notbaseCityDayReport->places_count_sent;
                    }
                    break;
                case 'child_count_sent_2':
                    if($model->notbaseCityDayReport != null && $model->notbaseCityDayReport->child_count_sent != null) {
                        $summ += $model->notbaseCityDayReport->child_count_sent;
                    }
                    break;

                case 'student_count_sent_2':
                    if($model->notbaseCityDayReport != null && $model->notbaseCityDayReport->student_count_sent != null) {
                        $summ += $model->notbaseCityDayReport->student_count_sent;
                    }
                    break;

                case 'prize_trip_count_sent_2':
                    if($model->notbaseCityDayReport != null && $model->notbaseCityDayReport->prize_trip_count_sent != null) {
                        $summ += $model->notbaseCityDayReport->prize_trip_count_sent;
                    }
                    break;

                case 'suitcase_count_sent_2':
                    if($model->notbaseCityDayReport != null && $model->notbaseCityDayReport->suitcase_count_sent != null) {
                        $summ += $model->notbaseCityDayReport->suitcase_count_sent;
                    }
                    break;

                case 'bag_count_sent_2':
                    if($model->notbaseCityDayReport != null && $model->notbaseCityDayReport->bag_count_sent != null) {
                        $summ += $model->notbaseCityDayReport->bag_count_sent;
                    }
                    break;

                case 'oversized_count_sent_2':
                    if($model->notbaseCityDayReport != null && $model->notbaseCityDayReport->oversized_count_sent != null) {
                        $summ += $model->notbaseCityDayReport->oversized_count_sent;
                    }
                    break;

                case 'is_not_places_count_sent_2':
                    if($model->notbaseCityDayReport != null && $model->notbaseCityDayReport->is_not_places_count_sent != null) {
                        $summ += $model->notbaseCityDayReport->is_not_places_count_sent;
                    }
                    break;
//                case 'airport_count_sent_2':
//                    if($model->notbaseCityDayReport != null && $model->notbaseCityDayReport->airport_count_sent != null) {
//                        $summ += $model->notbaseCityDayReport->airport_count_sent;
//                    }
//                    break;
                case 'airport_places_count_sent_2':
                    if($model->notbaseCityDayReport != null && $model->notbaseCityDayReport->airport_places_count_sent != null) {
                        $summ += $model->notbaseCityDayReport->airport_places_count_sent;
                    }
                    break;
                case 'proceeds_2':
                    if($model->notbaseCityDayReport != null && $model->notbaseCityDayReport->proceeds != null) {
                        $summ += $model->notbaseCityDayReport->proceeds;
                    }
                    break;

                case 'total_proceeds':
                    $summ += $model->total_proceeds;
                    break;

                case 'formula_percent':
                    if($model->transport != null && $model->transport->formula != null) {
                        $summ += $model->transport->formula->getResult($model->total_proceeds);
                    }
                    break;


                default:
                    $summ += $model->$field_name;
                    break;
            }
        }

        return $summ;

    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'transport_id', 'base_city_trip_id', 'base_city_trip_start_time', 'base_city_day_report_id',
                'notbase_city_trip_id', 'notbase_city_trip_start_time', 'notbase_city_day_report_id',
                'state'], 'integer'],
            [['total_proceeds', 'total_paid_summ'], 'number'],
            [['event'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params, $date, $pagination = true)
    {
        // add conditions that should always apply here

        if (!is_null($date) && strpos($date, '-') !== false) {

            list($dateStart, $dateEnd) = explode('-', $date);

            $query = DayReportTransportCircle::find()
                ->leftJoin('day_report_trip_transport dr1', '`dr1`.`id` = `day_report_transport_circle`.`base_city_day_report_id`')
                ->leftJoin('day_report_trip_transport dr2', '`dr2`.`id` = `day_report_transport_circle`.`notbase_city_day_report_id`')
                ->where([
                    'OR',
                    ['BETWEEN', 'dr1.date', strtotime($dateStart), strtotime($dateEnd)],
                    ['BETWEEN', 'dr2.date', strtotime($dateStart), strtotime($dateEnd)],
                ]);
        }else {

            $query = DayReportTransportCircle::find()
                ->leftJoin('day_report_trip_transport dr1', '`dr1`.`id` = `day_report_transport_circle`.`base_city_day_report_id`')
                ->leftJoin('day_report_trip_transport dr2', '`dr2`.`id` = `day_report_transport_circle`.`notbase_city_day_report_id`')
                ->where([
                    'OR',
                    ['dr1.date' => strtotime($date)],
                    ['dr2.date' => strtotime($date)]
                ]);
        }

        if($pagination == true) {
            $dataProvider = new ActiveDataProvider([
                'query' => $query,
                'pagination' => [
                    'pageSize' => Yii::$app->session->get('table-rows', 20)
                ],
            ]);
        }else {
            $dataProvider = new ActiveDataProvider([
                'query' => $query,
                'pagination' => false,
            ]);
        }


        // эти установки "сортировки" нужны чтобы в таблицах отображались ссылки на именах колонок
        $dataProvider->setSort([
            'attributes' => array_merge($dataProvider->getSort()->attributes, [
//                'transport_id' => [
//                    'asc' => ['transport_id' => SORT_ASC],
//                    'desc' => ['transport_id' => SORT_DESC]
//                ],
                'direction_name_1' => [
                    'asc' => ['dr1.direction_name' => SORT_ASC],
                    'desc' => ['dr1.direction_name' => SORT_DESC]
                ],
                'trip_name_1' => [
                    'asc' => ['dr1.trip_name' => SORT_ASC],
                    'desc' => ['dr1.trip_name' => SORT_DESC]
                ],
                'transport_car_reg_1' => [
                    'asc' => ['dr1.transport_car_reg' => SORT_ASC],
                    'desc' => ['dr1.transport_car_reg' => SORT_DESC]
                ],

                'places_count_sent_1' => [
                    'asc' => ['dr1.places_count_sent' => SORT_ASC],
                    'desc' => ['dr1.places_count_sent' => SORT_DESC]
                ],
                'child_count_sent_1' => [
                    'asc' => ['dr1.child_count_sent' => SORT_ASC],
                    'desc' => ['dr1.child_count_sent' => SORT_DESC]
                ],
                'student_count_sent_1' => [
                    'asc' => ['dr1.student_count_sent' => SORT_ASC],
                    'desc' => ['dr1.student_count_sent' => SORT_DESC]
                ],
                'prize_trip_count_sent_1' => [
                    'asc' => ['dr1.prize_trip_count_sent' => SORT_ASC],
                    'desc' => ['dr1.prize_trip_count_sent' => SORT_DESC]
                ],
                'bag_count_sent_1' => [
                    'asc' => ['dr1.bag_count_sent' => SORT_ASC],
                    'desc' => ['dr1.bag_count_sent' => SORT_DESC]
                ],
                'is_not_places_count_sent_1' => [
                    'asc' => ['dr1.is_not_places_count_sent' => SORT_ASC],
                    'desc' => ['dr1.is_not_places_count_sent' => SORT_DESC]
                ],
                'airport_count_sent_1' => [
                    'asc' => ['dr1.airport_count_sent' => SORT_ASC],
                    'desc' => ['dr1.airport_count_sent' => SORT_DESC]
                ],
                'proceeds_1' => [
                    'asc' => ['dr1.proceeds' => SORT_ASC],
                    'desc' => ['dr1.proceeds' => SORT_DESC]
                ],


                'direction_name_2' => [
                    'asc' => ['dr2.direction_name' => SORT_ASC],
                    'desc' => ['dr2.direction_name' => SORT_DESC]
                ],
                'trip_name_2' => [
                    'asc' => ['dr2.trip_name' => SORT_ASC],
                    'desc' => ['dr2.trip_name' => SORT_DESC]
                ],
                'transport_car_reg_2' => [
                    'asc' => ['dr2.transport_car_reg' => SORT_ASC],
                    'desc' => ['dr2.transport_car_reg' => SORT_DESC]
                ],
                'places_count_sent_2' => [
                    'asc' => ['dr2.places_count_sent' => SORT_ASC],
                    'desc' => ['dr2.places_count_sent' => SORT_DESC]
                ],
                'child_count_sent_2' => [
                    'asc' => ['dr2.child_count_sent' => SORT_ASC],
                    'desc' => ['dr2.child_count_sent' => SORT_DESC]
                ],
                'student_count_sent_2' => [
                    'asc' => ['dr2.student_count_sent' => SORT_ASC],
                    'desc' => ['dr2.student_count_sent' => SORT_DESC]
                ],
                'prize_trip_count_sent_2' => [
                    'asc' => ['dr2.prize_trip_count_sent' => SORT_ASC],
                    'desc' => ['dr2.prize_trip_count_sent' => SORT_DESC]
                ],
                'bag_count_sent_2' => [
                    'asc' => ['dr2.bag_count_sent' => SORT_ASC],
                    'desc' => ['dr2.bag_count_sent' => SORT_DESC]
                ],
                'is_not_places_count_sent_2' => [
                    'asc' => ['dr2.is_not_places_count_sent' => SORT_ASC],
                    'desc' => ['dr2.is_not_places_count_sent' => SORT_DESC]
                ],
                'airport_count_sent_2' => [
                    'asc' => ['dr2.airport_count_sent' => SORT_ASC],
                    'desc' => ['dr2.airport_count_sent' => SORT_DESC]
                ],
                'proceeds_2' => [
                    'asc' => ['dr2.proceeds' => SORT_ASC],
                    'desc' => ['dr2.proceeds' => SORT_DESC]
                ],
                'total_proceeds' => [
                    'asc' => ['total_proceeds' => SORT_ASC],
                    'desc' => ['total_proceeds' => SORT_DESC]
                ],
            ])
        ]);

        $this->load($params);



        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'transport_id' => $this->transport_id,
            'base_city_trip_id' => $this->base_city_trip_id,
            'base_city_trip_start_time' => $this->base_city_trip_start_time,
            'base_city_day_report_id' => $this->base_city_day_report_id,
            'notbase_city_trip_id' => $this->notbase_city_trip_id,
            'notbase_city_trip_start_time' => $this->notbase_city_trip_start_time,
            'notbase_city_day_report_id' => $this->notbase_city_day_report_id,
            'state' => $this->state,
            'total_proceeds' => $this->total_proceeds,
            'total_paid_summ' => $this->total_paid_summ,
        ]);

        return $dataProvider;
    }
}
