<?php

use yii\db\Migration;

/**
 * Class m181108_221841_add_fields_to_waybill
 */
class m181108_221841_add_fields_to_waybill extends Migration
{
    public function up()
    {
//        Предварительные итоги рейса:
//        - сумма принятых расходов из выручки - accepted_expenses_from_revenue
//        - сумма непринятых расходов из выручки - not_accepted_expenses_from_revenue
//        - входящие требования на общую сумму - incoming_requirements
//        - принятые расходы всех типов - accepted_expenses_all_types
//
//      Корректировка:
//        - По камерам - camera_val
//        - Из них указано водителем - camera_driver_val
//        - Вычет, руб - camera_eduction
//        - Без записи, руб - camera_no_record
//        - Без записи, расшифровка - camera_no_record_comment
//
//        - сдано B1 (руб) - hand_over_b1
//        - дата - hand_over_b1_data
//        - сдано B2 (руб) - hand_over_b2
//        - дата - hand_over_b2_data
//        - Расшифровка данных коррекции - correct_comment


        // Предварительные итоги рейса
        $this->addColumn('transport_waybill', 'accepted_expenses_from_revenue',  $this->decimal(8, 2)->defaultValue(0)->comment('Сумма принятых расходов из выручки'));
        $this->addColumn('transport_waybill', 'not_accepted_expenses_from_revenue',  $this->decimal(8, 2)->defaultValue(0)->comment('Сумма непринятых расходов из выручки'));
        $this->addColumn('transport_waybill', 'incoming_requirements',  $this->decimal(8, 2)->defaultValue(0)->comment('Входящие требования на общую сумму'));
        $this->addColumn('transport_waybill', 'accepted_expenses_all_types',  $this->decimal(8, 2)->defaultValue(0)->comment('Принятые расходы всех типов'));

        // Корректировка
        $this->addColumn('transport_waybill', 'camera_val',  $this->integer()->comment('По камерам'));
        $this->addColumn('transport_waybill', 'camera_driver_val',  $this->integer()->comment('Из них указано водителем'));
        $this->addColumn('transport_waybill', 'camera_eduction',  $this->decimal(8, 2)->defaultValue(0)->comment('Вычет, руб'));
        $this->addColumn('transport_waybill', 'camera_no_record',  $this->decimal(8, 2)->defaultValue(0)->comment('Без записи, руб'));
        $this->addColumn('transport_waybill', 'camera_no_record_comment', $this->string(255)->comment('Без записи, расшифровка'));

        $this->addColumn('transport_waybill', 'hand_over_b1',  $this->decimal(8, 2)->defaultValue(0)->comment('сдано B1'));
        $this->addColumn('transport_waybill', 'hand_over_b1_data',  $this->integer()->comment('Дата (когда сдано B1)'));
        $this->addColumn('transport_waybill', 'hand_over_b2',  $this->decimal(8, 2)->defaultValue(0)->comment('сдано B2'));
        $this->addColumn('transport_waybill', 'hand_over_b2_data',  $this->integer()->comment('Дата (когда сдано B2)'));
        $this->addColumn('transport_waybill', 'correct_comment',  $this->string(255)->comment('Расшифровка данных коррекции'));

//      Начисления:
//          К выдаче на рейс (руб) - accruals_to_issue_for_trip
//          Выдано на руки (руб) - accruals_given_to_hand
//          Штрафы ГИБДД (руб) - fines_gibdd
//          Комментарий к штрафам ГИБДД - fines_gibdd_comment
//          Прочие штрафы - another_fines
//          Комментарий к прочим штрафам - another_fines_comment
//
//
//          Итого:
//          Чистая прибыль (руб) - total_net_profit
//          Фактически выдано (руб) - total_actually_given
//          Недосдача (руб) - total_failure_to_pay
//          Штрафы к оплате - total_fines


        // Начисления
        $this->addColumn('transport_waybill', 'accruals_to_issue_for_trip',  $this->decimal(8, 2)->defaultValue(0)->comment('К выдаче на рейс'));
        $this->addColumn('transport_waybill', 'accruals_given_to_hand',  $this->decimal(8, 2)->defaultValue(0)->comment('Выдано на руки'));
        $this->addColumn('transport_waybill', 'fines_gibdd',  $this->decimal(8, 2)->defaultValue(0)->comment('Штрафы ГИБДД'));
        $this->addColumn('transport_waybill', 'fines_gibdd_comment',  $this->string(255)->comment('Комментарий к штрафам ГИБДД'));
        $this->addColumn('transport_waybill', 'another_fines',  $this->decimal(8, 2)->defaultValue(0)->comment('Прочие штрафы'));
        $this->addColumn('transport_waybill', 'another_fines_comment', $this->string(255)->comment('Комментарий к штрафам ГИБДД'));

        // Начисления - Итого
        $this->addColumn('transport_waybill', 'total_net_profit',  $this->decimal(8, 2)->defaultValue(0)->comment('Чистая прибыль'));
        $this->addColumn('transport_waybill', 'total_actually_given',  $this->decimal(8, 2)->defaultValue(0)->comment('Фактически выдано'));
        $this->addColumn('transport_waybill', 'total_failure_to_pay',  $this->decimal(8, 2)->defaultValue(0)->comment('Недосдача'));
        $this->addColumn('transport_waybill', 'total_fines',  $this->decimal(8, 2)->defaultValue(0)->comment('Штрафы к оплате'));
    }

    public function down()
    {
        $this->dropColumn('transport_waybill', 'accepted_expenses_from_revenue');
        $this->dropColumn('transport_waybill', 'not_accepted_expenses_from_revenue');
        $this->dropColumn('transport_waybill', 'incoming_requirements');
        $this->dropColumn('transport_waybill', 'accepted_expenses_all_types');

        $this->dropColumn('transport_waybill', 'camera_val');
        $this->dropColumn('transport_waybill', 'camera_driver_val');
        $this->dropColumn('transport_waybill', 'camera_eduction');
        $this->dropColumn('transport_waybill', 'camera_no_record');
        $this->dropColumn('transport_waybill', 'camera_no_record_comment');

        $this->dropColumn('transport_waybill', 'hand_over_b1');
        $this->dropColumn('transport_waybill', 'hand_over_b1_data');
        $this->dropColumn('transport_waybill', 'hand_over_b2');
        $this->dropColumn('transport_waybill', 'hand_over_b2_data');
        $this->dropColumn('transport_waybill', 'correct_comment');


        $this->dropColumn('transport_waybill', 'accruals_to_issue_for_trip');
        $this->dropColumn('transport_waybill', 'accruals_given_to_hand');
        $this->dropColumn('transport_waybill', 'fines_gibdd');
        $this->dropColumn('transport_waybill', 'fines_gibdd_comment');
        $this->dropColumn('transport_waybill', 'another_fines');
        $this->dropColumn('transport_waybill', 'another_fines_comment');

        $this->dropColumn('transport_waybill', 'total_net_profit');
        $this->dropColumn('transport_waybill', 'total_actually_given');
        $this->dropColumn('transport_waybill', 'total_failure_to_pay');
        $this->dropColumn('transport_waybill', 'total_fines');
    }
}
