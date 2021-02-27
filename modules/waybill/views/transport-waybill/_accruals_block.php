<?php
use app\models\Access;

?>
<div class="waybill-block">
    <div class="waybill-title waybill-block-toogle">Начисления <span class="waybill-arrow"><i class="glyphicon glyphicon-menu-down"></i></span></div>
    <div class="waybill-body">

        <?php
        if(Access::hasUserAccess('access_to_delivery_of_proceeds', 'page_part'))
        { ?>
        <div class="row">
            <div class="col-v-45">
                <div class="row form-group-sm">
                    <div class="col-v-40">
                        <label style="font-weight: 100;">К выдаче за рейс</label>
                        <?php
                        $model->accruals_to_issue_for_trip = str_replace('.', ',', $model->accruals_to_issue_for_trip);
                        echo $form->field($model, 'accruals_to_issue_for_trip', [
                            'errorOptions' => ['style' => 'display:none;']
                        ])->textInput(['maxlength' => true])->label(false);
                        ?>
                    </div>
                    <div class="col-v-40">
                        <label style="font-weight: 100;">Выдано на руки</label>
                        <?php
                        $model->accruals_given_to_hand = str_replace('.', ',', $model->accruals_given_to_hand);
                        echo $form->field($model, 'accruals_given_to_hand', [
                            'errorOptions' => ['style' => 'display:none;']
                        ])->textInput(['maxlength' => true])->label(false);
                        ?>
                    </div>
                </div>

                <div class="row form-group-sm">
                    <div class="col-v-40">
                        <label style="font-weight: 100;">Штрафы ГИБДД</label>
                        <?php
                        $model->fines_gibdd = str_replace('.', ',', $model->fines_gibdd);
                        echo $form->field($model, 'fines_gibdd', [
                            'errorOptions' => ['style' => 'display:none;']
                        ])->textInput(['maxlength' => true])->label(false);
                        ?>
                    </div>
                    <div class="col-v-55">
                        <label style="font-weight: 100;">И комментарий к ним</label>
                        <?php
//                        echo $form->field($model, 'fines_gibdd_comment', [
//                            'errorOptions' => ['style' => 'display:none;']
//                        ])->textInput(['maxlength' => true])->label(false);
                        echo $form->field($model, 'fines_gibdd_comment')->textarea(['rows' => 2])->label(false);
                        ?>
                    </div>
                </div>

                <div class="row form-group-sm">
                    <div class="col-v-40">
                        <label style="font-weight: 100;">Прочие штрафы</label>
                        <?php
                        $model->another_fines = str_replace('.', ',', $model->another_fines);
                        echo $form->field($model, 'another_fines', [
                            'errorOptions' => ['style' => 'display:none;']
                        ])->textInput(['maxlength' => true])->label(false);
                        ?>
                    </div>
                    <div class="col-v-55">
                        <label style="font-weight: 100;">И комментарий к ним</label>
                        <?php
//                        echo $form->field($model, 'another_fines_comment', [
//                            'errorOptions' => ['style' => 'display:none;']
//                        ])->textInput(['maxlength' => true])->label(false);
                        echo $form->field($model, 'another_fines_comment')->textarea(['rows' => 2])->label(false);
                        ?>
                    </div>
                </div>

            </div>



            <div class="col-v-2">&nbsp;</div>
            <div class="col-v-45">

                <?php if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin'])) { ?>
                    <div class="row">
                        <div id="waybill-result-turquoise-block">
                            <table style="width: 100%;">
                                <tr>
                                    <td>Чистая прибыль по ПЛ:</td>
                                    <td><?= $model->total_net_profit ?></td>
                                </tr>
                                <tr>
                                    <td>Фактически выдано:</td>
                                    <td><?= $model->total_actually_given  ?></td>
                                </tr>
                                <tr>
                                    <td>Недосдача:</td>
                                    <td><?= $model->total_failure_to_pay ?></td>
                                </tr>
                                <tr>
                                    <td>Штрафы к оплате:</td>
                                    <td><?= $model->total_fines ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                <?php } ?>
            </div>

        </div>

        <?php
        }else {
            echo 'Нет доступа <br />';
        }
        ?>
    </div>
</div>
