<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\date\DatePicker;
use app\widgets\periodPicker\PeriodPicker;
use yii\widgets\LinkPager;
use kartik\export\ExportMenu;
use app\helpers\table\PageSizeHelper;

$this->title = 'Отчет текущих дней';
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile('js/admin/pages.js', ['depends' => 'app\assets\AdminAsset']);

$pagination = $dataProvider->getPagination();
$pagination->totalCount = $dataProvider->getTotalCount();


$columns = \app\models\DayReportTransportCircleSearch::getGridColumns($dataProvider, $date);
?>

<div class="day-report-transport-circle" class="box box-default">

    <div class="box-header scroller with-border">

        <div class="pull-left" style="width: 140px; display: inline-block;">
            <?php
//            echo DatePicker::widget([
//                'name' => 'date',
//                'value' => date('d.m.Y', $unixdate),
//                'removeButton' => false,
//                'pluginOptions' => [
//                    'format' => 'dd.mm.yyyy',
//                    'autoclose' => true,
//                ],
//            ]);

            echo PeriodPicker::widget([
                'name' => 'date',
                'value' => $date,
                'isFilterInGridView' => false,
                'clearButton' => false,

                'onOkButtonClick' => 'function () {

                    //alert("start=" + this.startinput.val() + " end=" + this.endinput.val())
                    var date = this.startinput.val() + "-" + this.endinput.val();
                    location.href = "/admin/day-report-transport-circle/index?DayReportTransportCircleSearch[date]=" + date;
                }',

                'options' => [
                    'formatDecoreDate' => 'D.MM',
                    'formatDecoreDateWithYear' => 'D.MM.YY',
                    'timepicker' => false,
                    'draggable' => false,
                    'i18n' => [
                        'ru' => ['Choose period'=> 'за всё время']
                    ],
                ],
            ]);


            ?>
        </div>
        <div class="pull-left">
            <?php
            echo '<div style="vertical-align: top; display: inline-block;">'.ExportMenu::widget([
                    'dataProvider' => $dataProviderWithoutPagination,
                    'columns' => $columns,
                    'fontAwesome' => true,
                    //'target' => ExportMenu::TARGET_SELF,
                    /*'exportConfig' => [
//                        ExportMenu::FORMAT_HTML => true,
//                        ExportMenu::FORMAT_TEXT => false,
//                        ExportMenu::FORMAT_HTML => true,
//                        ExportMenu::FORMAT_CSV => true,
//                        ExportMenu::FORMAT_TEXT => true,
//                        ExportMenu::FORMAT_PDF => true,
//                        ExportMenu::FORMAT_EXCEL => true,
//                        ExportMenu::FORMAT_EXCEL_X => true

                        ExportMenu::FORMAT_PDF => [
                            'label' => 'PDF',
                            //'icon' => $isFa ? 'file-pdf-o' : 'floppy-disk',
                            'icon' => 'file-pdf-o',
                            'iconOptions' => ['class' => 'text-danger'],
                            'linkOptions' => [],
                            'options' => ['title' => 'Portable Document Format'],
                            'alertMsg' => 'The PDF export file will be generated for download.',
                            'mime' => 'application/pdf',
                            'extension' => 'pdf',
                            'writer' => 'Csv'
                        ],
                    ],*/
                ]).'</div>';
            ?>
        </div>

        <div class="pull-left">
            <?= LinkPager::widget([
                'pagination' => $pagination,
                'options' => ['class' => 'pagination pagination-sm']
            ]); ?>
        </div>
        <?php
        echo (new PageSizeHelper([20, 50, 100, 200, 500]))->getButtons();
        ?>

    </div>

    <div></div>

    <div class="box-body box-table">
        <?php
        echo GridView::widget([
            'dataProvider' => $dataProvider,
            //'filterModel' => $searchModel,
            'options' => ['class' => 'grid-view table-responsive'],
            'tableOptions' => [
                'class' => 'table table-condensed table-bordered table-hover'
            ],
            'columns' => $columns
        ]); ?>
    </div>
</div>
