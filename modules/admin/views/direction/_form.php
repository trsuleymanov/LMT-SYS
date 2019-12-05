<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\City;
use yii\grid\GridView;
use kartik\date\DatePicker;
use yii\helpers\Url;


$this->registerJsFile('js/admin/pages.js', ['depends' => 'app\assets\AdminAsset']);
?>

<div class="box box-solid">

    <div class="box-header with-border">
        <h3 class="box-title">
            <i class="fa fa-address-book-o"></i>
            Редактирование направления
        </h3>
        <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse">
                <i class="fa fa-minus"></i>
            </button>
        </div>
    </div>

    <div class="box-body">

        <?php $form = ActiveForm::begin(); ?>

        <div class="row">
            <div class="col-sm-4 form-group form-group-sm">
                <?= $form->field($model, 'sh_name')->textInput(['maxlength' => true]) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-4 form-group form-group-sm">
                <?= $form->field($model, 'city_from')->dropDownList(ArrayHelper::map(City::find()->all(), 'id', 'name')); ?>
            </div>

            <div class="col-sm-4 form-group form-group-sm">
                <?= $form->field($model, 'city_to')->dropDownList(ArrayHelper::map(City::find()->all(), 'id', 'name')); ?>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-4 form-group form-group-sm">
                <?= $form->field($model, 'distance')->textInput() ?>
            </div>
        </div>


        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>

<?php
if(!$model->isNewRecord)
{ ?>

<div class="box box-solid">

    <div class="box-header with-border">
        <h3 class="box-title">
            <i class="fa fa-address-book-o"></i>
            Расписания планируемых рейсов
        </h3>
        <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse">
                <i class="fa fa-minus"></i>
            </button>
        </div>
    </div>

    <div class="box-body">

        <?php
        if($last_order == null) {
            $last_date = date('d.m.Y');
            ?><p style="font-size: 14px;">- на данном рейсе заказов не существует.</p><?php
        }else {
            $last_date = ($last_order->date > time() ? date('d.m.Y', $last_order->date) : date('d.m.Y'));
            ?>
            <p style="font-size: 14px;">- дата самого позднего заказа по текущему рейсу: <?= date('d.m.Y', $last_order->date) ?>.
                Значит расписания запущенные до <?= $last_date ?> не могут быть изменены.</p>
        <?php } ?>

        <a href="<?= Url::to(['/admin/schedule/create', 'direction_id' => $model->id]) ?>" class="btn btn-mini btn-success" type="submit">Добавить расписание</a>

        <?= GridView::widget([
            'dataProvider' => $dataScheduleProvider,
            //'filterModel' => $searchScheduleModel,
            'options' => ['class' => 'grid-view table-responsive'],
            'summary' => '',
            'tableOptions' => [
                'class' => 'table table-condensed table-bordered table-hover'
            ],
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                //'id',
                //'name',
                [
                    'attribute' => 'name',
                    'content' => function($model) {
                        return Html::a($model->name, Url::to(['/admin/schedule/view', 'id' => $model->id]));
                    }
                ],
                'start_date' => [
                    'label' => 'Дата запуска расписания',
                    'content' => function ($model) {
                        return date('d.m.Y', $model->start_date);
                    },
                ],
                'disabled_date' => [
                    'label' => 'Дата деактивации расписания',
                    'content' => function ($model) {
                        return (empty($model->disabled_date) ? '-' : date('d.m.Y', $model->disabled_date));
                    },
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{update} {delete}',
                    'options' => ['style' => 'width: 50px;'],
                    'buttons' => [
                        'update' => function ($url, $model) {
                            if($model->start_date > strtotime(date('d.m.Y'))) {
                                return Html::a(
                                    '<span class="glyphicon glyphicon-pencil"></span>',
                                    Url::to(['/admin/schedule/update', 'id' => $model->id]),
                                    ['aria-label' => 'Редактирование']
                                );
                            }else {
                                return '';
                            }
                        },
                        'delete' => function ($url, $model) {
                            if($model->start_date > strtotime(date('d.m.Y'))) {
                                return Html::a(
                                    '<span class="glyphicon glyphicon-trash"></span>',
                                    Url::to(['/admin/schedule/delete', 'id' => $model->id]),
                                    [
                                        'aria-label' => 'Удалить',
                                        'data-method' => "post",
                                        'data-pjax'  => 0,
                                        'data-confirm' => "Вы уверены, что хотите удалить этот элемент?",
                                    ]
                                );
                            }else {
                                return '';
                            }
                        },
                    ],
                ],
            ],
        ]);
        ?>
    </div>
</div>
<?php
}
?>
