<?php

use yii\db\Migration;

/**
 * Class m181103_015240_add_fields_to_transport_expenses
 */
class m181103_015240_add_fields_to_transport_expenses extends Migration
{
    public function up()
    {
        $this->truncateTable('transport_expenses');

        $this->addColumn('transport_expenses', 'need_pay_date', $this->integer()->comment('Дата документа - это дата входящего документа на требование оплаты')->after('payment_method_id'));
        $this->dropColumn('transport_expenses', 'expenses_seller_name');
        $this->dropColumn('transport_expenses', 'expenses_seller_id');

        $this->addColumn('transport_expenses', 'expenses_seller_type_id', $this->integer()->comment('Тип продавца (АЗС, Мойка, Стоянка, Прочие)')->after('expenses_type_id'));
        $this->addColumn('transport_expenses', 'expenses_seller_id', $this->integer()->comment('Наименование продавца')->after('check_attached'));

        $this->addColumn('transport_expenses', 'doc_number', $this->string(16)->comment('Номер документа')->after('transport_waybill_id'));
        $this->addColumn('transport_expenses', 'expenses_doc_type_id', $this->integer()->comment('Вид документа')->after('doc_number'));
        $this->dropColumn('transport_expenses', 'points');
        $this->addColumn('transport_expenses', 'points', $this->decimal(8, 2)->defaultValue(0)->comment('Баллы')->after('count'));
        $this->addColumn('transport_expenses', 'transport_expenses_paymenter_id', $this->integer()->comment('Кто оплатил')->after('payment_date'));
    }

    public function down()
    {
        $this->truncateTable('transport_expenses');

        $this->dropColumn('transport_expenses', 'need_pay_date');
        $this->addColumn('transport_expenses', 'expenses_seller_name', $this->string(100)->comment('Наименование продавца')->after('check_attached'));
        $this->dropColumn('transport_expenses', 'expenses_seller_type_id');
        $this->dropColumn('transport_expenses', 'expenses_seller_id');
        $this->addColumn('transport_expenses', 'expenses_seller_id', $this->integer()->comment('Наименование')->after('expenses_type_id'));
        $this->dropColumn('transport_expenses', 'expenses_doc_type_id');
        $this->dropColumn('transport_expenses', 'doc_number');
        $this->dropColumn('transport_expenses', 'points');
        $this->addColumn('transport_expenses', 'points', $this->smallInteger()->comment('Баллы')->after('count'));
        $this->dropColumn('transport_expenses', 'transport_expenses_paymenter_id');
    }
}
