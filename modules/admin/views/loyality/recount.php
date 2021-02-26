<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\LinkPager;
use app\helpers\table\PageSizeHelper;
use kartik\date\DatePicker;
use kartik\export\ExportMenu;

$this->title = 'Пересчет';
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile('js/admin/loyality.js', ['depends'=>'app\assets\AdminAsset']);


?>

<div id="recount-page" class="box box-default" >
    <div class="box-header scroller with-border">
        <div class="pull-left">

            <div class="row">
                <div class="col-sm-2 form-group form-group-sm">
                    <?= Html::submitButton('Пересчитать счетчики клиентов', ['id' => 'rewrite-clients-counters', 'class' => 'btn btn-success']) ?>
                    <br />
                    <div id="recount_clients_progress" style="display: none; width: 800px;">
                        <progress max="100" value="0" style="width: 100%;"></progress>
                    </div>
                </div>
            </div>

        </div>



    </div>
    <div></div>

    <div class="box-body box-table">


    </div>
</div>
