<?php
use app\models\NomenclatureDetail;
use app\models\Storage;
use app\models\StorageDetail;
use app\models\TransportDetailOrigin;
use app\models\TransportDetailState;
use app\models\TransportModel;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\date\DatePicker;
use app\widgets\periodPicker\PeriodPicker;


echo GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    //'layout'=>"{summary}\n{items}",
    'layout'=>"{items}",
    'tableOptions' => [
        'class' => 'table table-condensed table-bordered table-hover'
    ],
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],

        'id',
        [
            'attribute' => 'storage_id',
            'content' => function($model) {
                return
                    (mb_strlen($model->storage->name, 'UTF-8') > 5 ? mb_substr($model->storage->name, 0, 5, 'UTF-8').'.' : $model->storage->name);
            },
            'filter' => Html::activeDropDownList(
                $searchModel,
                'storage_id',
                ['' => 'Все'] + ArrayHelper::map(Storage::find()->all(), 'id', 'name'),
                ['class' => "form-control"]
            )
        ],
        [
            'label' => 'Наименование',
            'attribute' => 'nomenclature_detail_id',
            'content' => function($model) {
                //return $model->nomenclatureDetail != null ? $model->nomenclatureDetail->name : '';
                if($model->nomenclatureDetail != null) {
                    $detail_name = $model->nomenclatureDetail->detailName;
                    if($detail_name != null) {
                        return $detail_name->name;
                    }else {
                        return '';
                    }
                }else {
                    return '';
                }
            },
        ],
        [
            'attribute' => 'model_id',
            'label' => 'Тип т/с',
            'content' => function($model) {

                return $model->nomenclatureDetail != null ?
                    $model->nomenclatureDetail->transportModel != null ? $model->nomenclatureDetail->transportModel->name : ''
                    :
                    '';
            },
//            'filter' => Html::activeDropDownList(
//                $searchModel,
//                'model_id',
//                ['' => 'Все'] + ArrayHelper::map(TransportModel::find()->all(), 'id', 'name'),
//                ['class' => "form-control"]
//            )
        ],

        [
            'attribute' => 'installation_side',
            'label' => 'Геом.',
            'content' => function($model) {

                if($model->nomenclatureDetail == null) {
                    return '';
                }else {
                    return
                        ($model->nomenclatureDetail->installation_side > 0 ? $model->nomenclatureDetail->installationSide : '') .
                        ($model->nomenclatureDetail->installation_place > 0 ? ' ' . $model->nomenclatureDetail->installationPlace : '');
                }
            },
//            'filter' => Html::activeDropDownList(
//                $searchModel,
//                'installation_side',
//                ['' => 'Все'] + StorageDetail::getInstallationSides(),
//                ['class' => "form-control"]
//            )
        ],


        [
            'attribute' => 'detail_state_id',
            'label' => 'Состояние',
            'content' => function($model) {
                return $model->transportDetailState->name;
            },
            'filter' => Html::activeDropDownList(
                $searchModel,
                'detail_state_id',
                ['' => 'Все'] + ArrayHelper::map(TransportDetailState::find()->all(), 'id', 'name'),
                ['class' => "form-control"]
            )
        ],

        [
            'attribute' => 'detail_origin_id',
            'label' => 'Происхождение',
            'content' => function($model) {
                return $model->transportDetailOrigin->name;
            },
            'filter' => Html::activeDropDownList(
                $searchModel,
                'detail_origin_id',
                ['' => 'Все'] + ArrayHelper::map(TransportDetailOrigin::find()->all(), 'id', 'name'),
                ['class' => "form-control"]
            )
        ],

        [
            'attribute' => 'remainder',
            'headerOptions' => [
                'style' => 'width: 70px;',
            ],
            'label' => 'Ост.',
            'content' => function($model) {

                if($model->nomenclatureDetail == null) {
                    return '';
                }else {
                    return
                        $model->remainder
                        . ($model->nomenclatureDetail->detailMeasurementValue != null ? ' ' . $model->nomenclatureDetail->detailMeasurementValue->name : '');
                }
            },
        ],

        [
            'attribute' => 'comment',
            'content' => function($model) {
                return $model->comment;
            },
        ],

        [
            'attribute' => 'created_at',
            'label' => 'В.созд',
            'content' => function($model) {
                return ($model->created_at > 0 ? date("d.m.Y", $model->created_at) : '');
            },
//            'filter' => DatePicker::widget([
//                'model' => $searchModel,
//                'attribute' => 'created_at',
//                'type' => DatePicker::TYPE_COMPONENT_PREPEND,
//                'pluginOptions' => [
//                    'autoclose' => true,
//                    'format' => 'dd.mm.yyyy',
//                ]
//            ])
            'filter' => PeriodPicker::widget([
                'model' => $searchModel,
                'attribute' => 'created_at',
                'isFilterInGridView' => true,
                'clearButton' => false
            ])
        ],
        [
            'attribute' => 'updated_at',
            'label' => 'В.изм',
            'content' => function($model) {
                return ($model->updated_at > 0 ? date("d.m.Y", $model->updated_at) : '');
            },
//            'filter' => DatePicker::widget([
//                'model' => $searchModel,
//                'attribute' => 'updated_at',
//                'type' => DatePicker::TYPE_COMPONENT_PREPEND,
//                'pluginOptions' => [
//                    'autoclose' => true,
//                    'format' => 'dd.mm.yyyy',
//                ]
//            ])
            'filter' => PeriodPicker::widget([
                'model' => $searchModel,
                'attribute' => 'updated_at',
                'isFilterInGridView' => true,
                'clearButton' => false
            ])
        ],

//        [
//            'attribute' => 'detail_code',
//            'label' => 'Код запчасти',
//            'content' => function($model) {
//                //return $model->detail_code;
//                $model->nomenclatureDetail->detail_code;
//            },
//            'filter' => false,
//        ],
//        [
//            'attribute' => 'storage_place_count',
//            'content' => function($model) {
//                return $model->storage_place_count;
//            },
//        ],
        [
            'attribute' => 'refer_all_operation',
            'label' => '',
            'content' => function($model) {
                //return $model->storage_place_count;
                if($model->nomenclatureDetail != null && $model->nomenclatureDetail->detailName != null) {
                    return '<a href="/storage/operation?StorageOperationSearch[nomenclature_detail_name]=' . $model->nomenclatureDetail->detailName->name . '">Все операции по детали</a>';
                }else {
                    return '';
                }
            },
        ]

    ],
]);

?>