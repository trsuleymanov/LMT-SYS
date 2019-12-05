<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Transport;
use yii\widgets\MaskedInput;
use app\models\User;
use app\widgets\SelectWidget;
use yii\web\JsExpression;
use yii\helpers\Url;

//$arTransport = ArrayHelper::map(Transport::find()->all(), 'id', 'name');
?>

<div class="driver-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin'])) { ?>
        <div class="row">
            <div class="col-sm-4 form-group form-group-sm">
                <?= $form->field($model, 'accountability')->checkbox()->label(false); ?>
            </div>
        </div>
    <?php } ?>

    <div class="row">
        <div class="col-sm-4 form-group form-group-sm">
            <?= $form->field($model, 'fio')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="col-sm-4 form-group form-group-sm">
            <?php
            $createUser = Html::a(
                '&nbsp;<i class="fa fa-lg fa-plus-circle text-muted full-opacity-hover"></i>',
                '/admin/user/create',
                [
                    'target' => '_blank',
                    'class' => 'pull-right',
                    'data-toggle' => 'tooltip',
                    'title' => 'Создать пользователя в новой вкладке'
                ]
            );

            echo $form->field($model, 'user_id')->widget(SelectWidget::className(), [
                'initValueText' => ($model != null && $model->user != null ? $model->user->fullname : ''),
                'options' => [
                    'placeholder' => 'Введите название...',
                    'id' => 'user_id',
                ],
                'ajax' => [
                    'url' => '/user/ajax-get-users',
                    'data' => new JsExpression('function(params) {
                        return {
                            search: params.search
                        };
                    }'),
                ],
                'open_url' => new JsExpression('function(params) {
                    return "/admin/user/update?id=" + $("#user_id").val();
                }'),
            ])->label('Пользователь ' . $createUser);

            ?>
        </div>

        <div class="col-sm-4 form-group form-group-sm">
            <?php
            $options = [
                'id' => 'create-driver-user',
                'driver-id' => $model->id,
                'class' => 'btn-success',
            ];
            if($model->isNewRecord) {
                $options['class'] .= ' disabled';
            }
            echo Html::button('Создать пользователя с именем как у водителя и паролем 123456', $options) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-4 form-group form-group-sm">
            <?= $form->field($model, 'mobile_phone')
                ->textInput(['maxlength' => true])
                ->widget(\yii\widgets\MaskedInput::class, [
                    'mask' => '+7-999-999-9999',
                    'clientOptions' => [
                        'placeholder' => '*'
                    ]
                ]);
            ?>
        </div>

        <div class="col-sm-4 form-group form-group-sm">
            <?= $form->field($model, 'home_phone')
                ->textInput(['maxlength' => true])
                ->widget(\yii\widgets\MaskedInput::class, [
                    'mask' => '+7-999-999-9999',
                    'clientOptions' => [
                        'placeholder' => '*'
                    ]
                ]);
            ?>
        </div>

    </div>

    <div class="row">
        <div class="col-sm-4 form-group form-group-sm">
            <?php
            // echo $form->field($model, 'primary_transport_id')->dropDownList(['' => ''] + $arTransport);
//            echo SelectWidget::widget([
//                'model' => $model,
//                'attribute' => 'primary_transport_id',
//                'name' => 'primary_transport_id',
//                'initValueText' => ($model->primary_transport_id > 0 && $model->primary_transport_id != null ? $model->primaryTransport->name : ''),
//                'options' => [
//                    'placeholder' => 'Введите название...'
//                ],
//                'ajax' => [
//                    'url' => '/second-trip-transport/ajax-get-transports-names'
//                ],
//                'using_delete_button' => false
//            ]);


            echo $form->field($model, 'primary_transport_id')->widget(SelectWidget::classname(), [
                'name' => 'primary_transport_id',
                'initValueText' => ($model->primary_transport_id > 0 && $model->primary_transport_id != null ? $model->primaryTransport->name5 : ''),
                'options' => [
                    'placeholder' => 'Введите название...'
                ],
                'ajax' => [
                    'url' => '/second-trip-transport/ajax-get-transports-names'
                ],
                'using_delete_button' => false
            ]);
            ?>
        </div>

        <div class="col-sm-4 form-group form-group-sm">
            <?php
            //echo $form->field($model, 'secondary_transport_id')->dropDownList(['' => ''] + $arTransport);
//            echo SelectWidget::widget([
//                'model' => $model,
//                'attribute' => 'secondary_transport_id',
//                'name' => 'secondary_transport_id',
//                'initValueText' => ($model->secondary_transport_id > 0 && $model->secondary_transport_id != null ? $model->secondaryTransport->name : ''),
//                'options' => [
//                    'placeholder' => 'Введите название...'
//                ],
//                'ajax' => [
//                    'url' => '/second-trip-transport/ajax-get-transports-names'
//                ],
//                'using_delete_button' => false
//            ]);

            echo $form->field($model, 'secondary_transport_id')->widget(SelectWidget::classname(), [
                'name' => 'secondary_transport_id',
                'initValueText' => ($model->secondary_transport_id > 0 && $model->secondary_transport_id != null ? $model->secondaryTransport->name5 : ''),
                'options' => [
                    'placeholder' => 'Введите название...'
                ],
                'ajax' => [
                    'url' => '/second-trip-transport/ajax-get-transports-names'
                ],
                'using_delete_button' => false
            ]);
            ?>
        </div>

        <div class="col-sm-4 form-group form-group-sm">
            <?= $form->field($model, 'device_code')->textInput(['maxlength' => true]) ?>
        </div>
    </div>


    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
