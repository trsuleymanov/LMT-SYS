<?php
use app\models\TransportExpensesDocType;
use app\models\TransportExpensesPaymenter;
use app\models\TransportExpensesSeller;
use app\models\TransportExpensesSellerType;
use app\models\TransportExpensesTypes;
use app\models\TransportPaymentMethods;
use app\models\User;
use kartik\date\DatePicker;
use kartik\money\MaskMoney;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use app\widgets\SelectWidget;
use yii\web\JsExpression;


// Расходы
//            + $id
//            ??? $expenses_type_id Тип расходов
//            + $expenses_seller_type_id Наименование
//            + $price Сумма, руб
//            + $check_attached Чек прикреплен
//            + $expenses_seller_name Наименование продавца
//            + $count Количество
//            + $points Баллы

//            ??? $expenses_is_taken Расходы приняты
//            ??? $expenses_is_taken_comment Комментарий о принятии расходов
//            ??? $payment_method_id Способ оплаты
//            ??? $payment_date Дата оплаты
//            ??? $payment_comment Комментарий к оплате


//            * @property int $created_at Дата создания
//            * @property int $creator_id Создатель
//            * @property int $updated_at Дата изменения
//            * @property int $updator_id Изменитель

//echo "tr_expenses:<pre>"; print_r($tr_expenses); echo "</pre>";

$first_col_style = ($tr_expenses->expenses_is_taken != 1 ? 'background-color: #FFDBDB;' : 'background-color: #FFFFFF;');
?>


<tr class="transport-expenses" transport-expenses-id="<?= $tr_expenses->id ?>" style="height: 86px; <?= ($tr_expenses->expenses_is_taken != 1 ? 'background-color: #FFDBDB;' : '') ?>">

    <td style="height: 86px; <?= $first_col_style ?>">
        <?php
        if(in_array($tr_expenses->view_group, ['typical_expenses', ]) && $tr_expenses->price > 0) {
            echo $tr_expenses->sellerType->name.
                (in_array(Yii::$app->session->get('role_alias'), ['root', 'admin']) ? '&nbsp;&nbsp;&nbsp;<a class="move-expense-to-another-pl" expense-id="'.$tr_expenses->id.'" href="" title="Перенести расход в другой путевой лист"><i class="glyphicon glyphicon-share-alt"></i></a>' : '');
        }else {

            $seller_types = TransportExpensesSellerType::find()->all();

            echo SelectWidget::widget([
                'model' => $tr_expenses,
                'attribute' => 'expenses_seller_type_id',
                'id' => 'transportexpenses-expenses_seller_type_id-'.$tr_expenses->id,
                'name' => 'TransportExpenses['.$tr_expenses->id.'][expenses_seller_type_id]',
                'initValueText' => ($tr_expenses->expenses_seller_type_id > 0 && $tr_expenses->sellerType != null ? $tr_expenses->sellerType->name : ''),
                'options' => [
                    'placeholder' => 'Введите название...',
                    'class' => 'transportexpenses-expenses_seller_type_id',
                ],
                'ajax' => [
                    'url' => '/waybill/transport-waybill/ajax-get-sellers-types',
                    'data' => new JsExpression('function(params, obj) {
                        return {
                            search: params.search,
                        };
                    }'),
                ],
                'add_new_value_url' => new JsExpression('function(params, $obj) {
                    addNewSellerType($obj);
                }'),
                'using_delete_button' => false,
            ]);

            echo (in_array(Yii::$app->session->get('role_alias'), ['root', 'admin']) ? '<a class="move-expense-to-another-pl" style="padding: 5px 0 0 3px; display: block;" expense-id="'.$tr_expenses->id.'" href="" title="Перенести расход в другой путевой лист"><i class="glyphicon glyphicon-share-alt"></i></a>' : '');

        }
        ?>
    </td>
    <td>
        <?php

        echo SelectWidget::widget([
            'model' => $tr_expenses,
            'attribute' => 'expenses_seller_id',
            'id' => 'transportexpenses-expenses_seller_id-'.$tr_expenses->id,
            'name' => 'TransportExpenses['.$tr_expenses->id.'][expenses_seller_id]',
            'initValueText' => ($tr_expenses->expenses_seller_id > 0 && $tr_expenses->seller != null ? $tr_expenses->seller->name : ''),
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
            'add_new_value_url' => new JsExpression('function(params, $obj) {

                //var transport_expenses_id = $obj.parents(".transport-expenses").attr("transport-expenses-id");
                addNewSeller($obj);
            }'),
            'using_delete_button' => false
        ]);

        ?>
    </td>

    <td>
        <?php
        if($tr_expenses->count == 0) {
            $tr_expenses->count = '';
        }

        echo $form->field($tr_expenses, 'count', [
            'errorOptions' => ['style' => 'display:none;'],
            'options' => []
        ])->textInput([
            'maxlength' => true,
            'name' => 'TransportExpenses['.$tr_expenses->id.'][count]',
            'id' => 'transportexpenses-count-'.$tr_expenses->id,
            'class' => 'transportexpenses-count form-control',
            //'style' => 'width: 45px;'
        ])->label(false);
        ?>
        <a href="" class="open-transport-expenses-detailing">Дет-я</a>


    </td>

    <td>
        <?php
        if($tr_expenses->price == 0) {
            $tr_expenses->price = '';
        }

        echo $form->field($tr_expenses, 'price', [
            'errorOptions' => ['style' => 'display:none;'],
        ])
        ->textInput([
            'maxlength' => true,
            'name' => 'TransportExpenses['.$tr_expenses->id.'][price]',
            'id' => 'transportexpenses-price-'.$tr_expenses->id,
            'class' => 'transportexpenses-price form-control',
            //'style' => 'width: 60px;'
        ])
            ->label(false);


        ?>
    </td>

    <td>
        <?php
        if($tr_expenses->points == 0) {
            $tr_expenses->points = '';
        }

        echo $form->field($tr_expenses, 'points', [
            'errorOptions' => ['style' => 'display:none;']
        ])->textInput([
            'maxlength' => true,
            'name' => 'TransportExpenses['.$tr_expenses->id.'][points]',
            'id' => 'transportexpenses-points-'.$tr_expenses->id,
            'class' => 'transportexpenses-points form-control',
            //'style' => 'width: 60px;'
        ])->label(false);
        ?>
    </td>
    <td>
        <?php
        echo $form
            ->field($tr_expenses, 'expenses_doc_type_id', [
                'errorOptions' => ['style' => 'display:none;'],
            ])
            ->dropDownList(
                [0 => ''] + ArrayHelper::map(TransportExpensesDocType::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),
                [
                    'name' => 'TransportExpenses['.$tr_expenses->id.'][expenses_doc_type_id]',
                    'id' => 'transportexpenses-expenses_doc_type_id-'.$tr_expenses->id,
                    'class' => 'transportexpenses-expenses_doc_type_id form-control',
                    //'style' => 'width: 60px;'
                ]
            )->label(false);
        ?>
    </td>
    <td>
        <?php
        echo $form
            ->field($tr_expenses, 'expenses_type_id', [
                'errorOptions' => ['style' => 'display:none;'],
            ])
            ->dropDownList(
                [0 => ''] + ArrayHelper::map(TransportExpensesTypes::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),
                [
                    'name' => 'TransportExpenses['.$tr_expenses->id.'][expenses_type_id]',
                    'id' => 'transportexpenses-expenses_type_id-'.$tr_expenses->id,
                    'class' => 'transportexpenses-expenses_type_id form-control',
                    //'style' => 'width: 60px;'
                ]
            )->label(false);
        ?>
    </td>
    <td>
        <?php
        echo $form->field($tr_expenses, 'doc_number', [
            'errorOptions' => ['style' => 'display:none;']
        ])->textInput([
            'maxlength' => true,
            'name' => 'TransportExpenses['.$tr_expenses->id.'][doc_number]',
            'id' => 'transportexpenses-doc_number-'.$tr_expenses->id,
            'class' => 'transportexpenses-doc_number form-control',
            //'style' => 'width: 60px;'
        ])->label(false);
        ?>
    </td>
    <td>
        <?php
        if($tr_expenses->need_pay_date > 0 && !preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$/i', $tr_expenses->need_pay_date)) {
            $tr_expenses->need_pay_date = date("d.m.Y", $tr_expenses->need_pay_date);
        }
        echo $form->field($tr_expenses, 'need_pay_date', ['errorOptions' => ['style' => 'display:none;']])
            ->widget(kartik\date\DatePicker::classname(), [
                'type' => DatePicker::TYPE_INPUT,
                'pluginOptions' => [
                    'format' => 'dd.mm.yyyy',
                    'todayHighlight' => true,
                    'autoclose' => true,
                    'class' => ''
                ],
                'options' => [
                    //'id' => 'transportexpenses-need_pay_date_'.$tr_expenses->id,
                    'name' => 'TransportExpenses['.$tr_expenses->id.'][need_pay_date]',
                    'id' => 'transportexpenses-need_pay_date-'.$tr_expenses->id,
                    'class' => 'transportexpenses-need_pay_date form-control',
                    'placeholder' => '10.05.2017',
                    //'style' => 'width: 78px; padding: 0 2px;'
                    'style' => 'padding: 0 2px;'
                ]
            ])
            ->label(false);
        ?>
    </td>
    <td>
        <?php
        echo $form->field($tr_expenses, 'check_attached')->checkbox([
            'label' => false,
            'name' => 'TransportExpenses['.$tr_expenses->id.'][check_attached]',
            'id' => 'transportexpenses-check_attached-'.$tr_expenses->id,
            'class' => 'transportexpenses-check_attached form-control',
        ])->label(false);
        ?>
    </td>
    <td>
        <?php
        // если галка не отмечена, то расходы не приняты
        echo $form->field($tr_expenses, 'expenses_is_taken')->checkbox([
            'label' => false,
            'name' => 'TransportExpenses['.$tr_expenses->id.'][expenses_is_taken]',
            'id' => 'transportexpenses-expenses_is_taken-'.$tr_expenses->id,
            'class' => 'transportexpenses-expenses_is_taken form-control',
        ])->label(false);
        ?>
    </td>
    <td>
        <?php
//        echo $form->field($tr_expenses, 'expenses_is_taken_comment', [
//            'errorOptions' => ['style' => 'display:none;']
//        ])->textInput([
//            'maxlength' => true,
//            'name' => 'TransportExpenses['.$tr_expenses->id.'][expenses_is_taken_comment]',
//            'id' => 'transportexpenses-expenses_is_taken_comment-'.$tr_expenses->id,
//            'class' => 'transportexpenses-expenses_is_taken_comment form-control',
//            'style' => 'width: 60px;'
//        ])->label(false);

        echo $form->field($tr_expenses, 'expenses_is_taken_comment')->textarea([
            'rows' => 2,
            'name' => 'TransportExpenses['.$tr_expenses->id.'][expenses_is_taken_comment]',
            'id' => 'transportexpenses-expenses_is_taken_comment-'.$tr_expenses->id,
            'class' => 'transportexpenses-expenses_is_taken_comment form-control',
            'style' => 'min-height: 50px; '
        ])->label(false);
        ?>
    </td>
    <td>
        <?php
        if($tr_expenses->payment_date > 0 && !preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$/i', $tr_expenses->payment_date)) {
            $tr_expenses->payment_date = date("d.m.Y", $tr_expenses->payment_date);
        }
        echo $form->field($tr_expenses, 'payment_date', ['errorOptions' => ['style' => 'display:none;']])
            ->widget(kartik\date\DatePicker::classname(), [
                'type' => DatePicker::TYPE_INPUT,
                'pluginOptions' => [
                    'format' => 'dd.mm.yyyy',
                    'todayHighlight' => true,
                    'autoclose' => true,
                    'class' => ''
                ],
                'options' => [
                    //'id' => 'transportexpenses-payment_date_'.$tr_expenses->id,
                    'name' => 'TransportExpenses['.$tr_expenses->id.'][payment_date]',
                    'id' => 'transportexpenses-payment_date-'.$tr_expenses->id,
                    'class' => 'transportexpenses-payment_date form-control',
                    'placeholder' => '10.05.2017',
                    //'style' => 'width: 78px; padding: 0 2px;'
                    'style' => 'padding: 0 2px;'
                ]
            ])
            ->label(false);
        ?>
    </td>
    <td>
        <?php
        echo $form
            ->field($tr_expenses, 'payment_method_id', [
                'errorOptions' => ['style' => 'display:none;'],
            ])
            ->dropDownList(
                [0 => ''] + ArrayHelper::map(TransportPaymentMethods::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),
                [
                    'name' => 'TransportExpenses['.$tr_expenses->id.'][payment_method_id]',
                    'id' => 'transportexpenses-payment_method_id-'.$tr_expenses->id,
                    'class' => 'transportexpenses-payment_method_id form-control',
                    //'style' => 'width: 60px;'
                ]
            )->label(false);
        ?>
    </td>
    <td>
        <?php
        echo SelectWidget::widget([
            'model' => $tr_expenses,
            'attribute' => 'transport_expenses_paymenter_id',
            'id' => 'transportexpenses-transport_expenses_paymenter_id-'.$tr_expenses->id,
            'name' => 'TransportExpenses['.$tr_expenses->id.'][transport_expenses_paymenter_id]',
            'initValueText' => ($tr_expenses->transport_expenses_paymenter_id > 0 && $tr_expenses->paymenter != null ? $tr_expenses->paymenter->username : ''),
            'options' => [
                'placeholder' => 'Введите название...',
                'class' => 'transportexpenses-transport_expenses_paymenter_id form-control',
            ],
            'ajax' => [
                'url' => '/user/ajax-get-users',
                'data' => new JsExpression('function(params, obj) {
                    return {
                        search: params.search,
                    };
                }'),
            ],
            'using_delete_button' => false
        ]);
        ?>
    </td>
    <td>
        <?php
//        echo $form->field($tr_expenses, 'payment_comment', [
//            'errorOptions' => ['style' => 'display:none;']
//        ])->textInput([
//            'maxlength' => true,
//            'name' => 'TransportExpenses['.$tr_expenses->id.'][payment_comment]',
//            'id' => 'transportexpenses-payment_comment-'.$tr_expenses->id,
//            'class' => 'transportexpenses-payment_comment form-control',
//            'style' => 'width: 60px;'
//        ])->label(false);

        echo $form->field($tr_expenses, 'payment_comment')->textarea([
            'rows' => 2,
            'name' => 'TransportExpenses['.$tr_expenses->id.'][payment_comment]',
            'id' => 'transportexpenses-payment_comment-'.$tr_expenses->id,
            'class' => 'transportexpenses-payment_comment form-control',
            'style' => 'min-height: 50px; '
        ])->label(false);
        ?>
    </td>

    <?php
    if($delete_row == true) { ?>
        <td>
            <a class="btn btn-danger delete-transport-expenses" href="#">
                <i class="glyphicon glyphicon-remove"></i>
            </a>
        </td>
    <?php } ?>
</tr>

