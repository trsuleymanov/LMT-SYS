<?php

use app\models\Access;
use kartik\date\DatePicker;

?>
<div class="waybill-block">
    <div class="waybill-title waybill-block-toogle">Корректировка <span class="waybill-arrow"><i class="glyphicon glyphicon-menu-down"></i></span></div>
    <div class="waybill-body">

        <?php if(Access::hasUserAccess('access_to_delivery_of_proceeds', 'page_part'))
        { ?>
        <div class="row">
            <div class="col-v-45">
                <div class="row form-group-sm">
                    <label style="margin-left: 15px;">Данные камер:</label><br />
                    <div class="col-v-33" style="margin-top: 20px;">
                        <label style="font-weight: 100;">По камерам</label>
                        <?php
                        echo $form->field($model, 'camera_val', [
                            'errorOptions' => ['style' => 'display:none;']
                        ])->textInput(['maxlength' => true])->label(false);
                        ?>
                    </div>
                    <div class="col-v-33">
                        <label style="font-weight: 100;">Из них указано водителем</label>
                        <?php
                        echo $form->field($model, 'camera_driver_val', [
                            'errorOptions' => ['style' => 'display:none;']
                        ])->textInput(['maxlength' => true])->label(false);
                        ?>
                    </div>
                    <div class="col-v-33">
                        <label style="font-weight: 100; margin-top: 20px;">Вычет, руб</label>
                        <?php
                        $model->camera_eduction = str_replace('.', ',', $model->camera_eduction);
                        echo $form->field($model, 'camera_eduction', [
                            'errorOptions' => ['style' => 'display:none;']
                        ])->textInput(['maxlength' => true])->label(false);
                        ?>
                    </div>
                </div>

                <br />
                <div class="row form-group-sm">
                    <div class="col-v-40">
                        <label>Без записи, руб</label>
                        <?php
                        $model->camera_no_record = str_replace('.', ',', $model->camera_no_record);
                        echo $form->field($model, 'camera_no_record', [
                            'errorOptions' => ['style' => 'display:none;']
                        ])->textInput(['maxlength' => true])->label(false);
                        ?>
                    </div>
                    <div class="col-v-40">
                        <label>И расшифровка к ним</label>
                        <?php
//                        echo $form->field($model, 'camera_no_record_comment', [
//                            'errorOptions' => ['style' => 'display:none;']
//                        ])->textInput(['maxlength' => true])->label(false);
                        echo $form->field($model, 'camera_no_record_comment')->textarea(['rows' => 2])->label(false);
                        ?>
                    </div>
                </div>

            </div>
            <div class="col-v-2">&nbsp;</div>
            <div class="col-v-45">
                <div class="row">
                    <div id="waybill-result-yellow-block">
                        <table style="width: 100%;">
                            <tr>
                                <?php if(!in_array(Yii::$app->session->get('role_alias'), ['warehouse_turnover'])) { ?>
                                    <td></td>
                                    <td><label>Сдано</label></td>
                                    <td><label>Дата</label></td>
                                    <td>&nbsp;</td>
                                <?php }else { ?>
                                    <td colspan="4">&nbsp;</td>
                                <?php } ?>
                                <td><label>Недосдача:</label></td>
                                <td></td>
                            </tr>
                            <tr>
                                <?php if(!in_array(Yii::$app->session->get('role_alias'), ['warehouse_turnover'])) { ?>

                                    <td>&nbsp;&nbsp;&nbsp;<label>B1</label>&nbsp;&nbsp;</td>
                                    <td class="form-group-sm">
                                        <?php
                                        $model->hand_over_b1 = str_replace('.', ',', $model->hand_over_b1);
                                        echo $form->field($model, 'hand_over_b1', [
                                            'errorOptions' => ['style' => 'display:none;']
                                        ])->textInput([
                                            'maxlength' => true,
                                            'style' => 'width: 95px;'
                                        ])->label(false);
                                        ?>
                                    </td>
                                    <td class="form-group-sm">
                                        <?php
                                        if($model->hand_over_b1_data > 0 && !preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$/i', $model->hand_over_b1_data)) {
                                            $model->hand_over_b1_data = date("d.m.Y", $model->hand_over_b1_data);
                                        }
                                        echo $form->field($model, 'hand_over_b1_data', ['errorOptions' => ['style' => 'display:none;']])
                                            ->widget(kartik\date\DatePicker::classname(), [
                                                'type' => DatePicker::TYPE_INPUT,
                                                'pluginOptions' => [
                                                    'format' => 'dd.mm.yyyy',
                                                    'todayHighlight' => true,
                                                    'autoclose' => true,
                                                    'class' => '',
                                                ],
                                                'options' => [
                                                    'style' => 'width: 105px;'
                                                ]
                                            ])
                                            ->widget(\yii\widgets\MaskedInput::class, [
                                                'clientOptions' => [
                                                    'alias' =>  'dd.mm.yyyy',
                                                ],
                                                'options' => [
                                                    'aria-required' => 'true',
                                                    'placeholder' => '10.05.2017',
                                                    'class' => 'form-control',
                                                    'style' => 'width: 105px;'
                                                ]

                                            ])
                                            ->label(false);
                                        ?>
                                    </td>
                                    <td></td>
                                <?php }else { ?>
                                    <td colspan="4">&nbsp;</td>
                                <?php } ?>
                                <td id="total-failure-to-pay"><?= $model->total_failure_to_pay ?></td>
                                <td></td>
                            </tr>
                            <tr>
                                <?php if(!in_array(Yii::$app->session->get('role_alias'), ['warehouse_turnover'])) { ?>

                                    <td>&nbsp;&nbsp;&nbsp;<label>B2</label>&nbsp;&nbsp;</td>
                                    <td class="form-group-sm"><?php
                                        $model->hand_over_b2 = str_replace('.', ',', $model->hand_over_b2);
                                        echo $form->field($model, 'hand_over_b2', [
                                            'errorOptions' => ['style' => 'display:none;']
                                        ])->textInput([
                                            'maxlength' => true,
                                            'style' => 'width: 95px;'
                                        ])->label(false);
                                        ?></td>
                                    <td class="form-group-sm">
                                        <?php
                                        if($model->hand_over_b2_data > 0 && !preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$/i', $model->hand_over_b2_data)) {
                                            $model->hand_over_b2_data = date("d.m.Y", $model->hand_over_b2_data);
                                        }
                                        echo $form->field($model, 'hand_over_b2_data', ['errorOptions' => ['style' => 'display:none;']])
                                            ->widget(kartik\date\DatePicker::classname(), [
                                                'type' => DatePicker::TYPE_INPUT,
                                                'pluginOptions' => [
                                                    'format' => 'dd.mm.yyyy',
                                                    'todayHighlight' => true,
                                                    'autoclose' => true,
                                                    'class' => '',
                                                ],
                                                'options' => [
                                                    'style' => 'width: 105px;'
                                                ]
                                            ])
                                            ->widget(\yii\widgets\MaskedInput::class, [
                                                'clientOptions' => [
                                                    'alias' =>  'dd.mm.yyyy',
                                                ],
                                                'options' => [
                                                    'aria-required' => 'true',
                                                    'placeholder' => '10.05.2017',
                                                    'class' => 'form-control',
                                                    'style' => 'width: 105px;'
                                                ]

                                            ])
                                            ->label(false);
                                        ?>
                                    </td>
                                    <td></td>
                                <?php }else { ?>
                                    <td colspan="4">&nbsp;</td>
                                <?php } ?>
                                <td></td>
                                <td></td>
                            </tr>
                        </table>

                    </div>
                </div>
                <br />
                <div class="row">
                    <?= $form->field($model, 'correct_comment')->textarea(['rows' => 2]); ?>
                </div>
            </div>
        </div>
        <?php
        }else {
            echo 'Нет доступа <br />';
        } ?>
    </div>
</div>
