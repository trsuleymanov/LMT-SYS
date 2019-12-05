<?php

use yii\db\Migration;

/**
 * Class m190517_181845_add_cashback_fields_to_order
 */
class m190517_181845_add_cashback_fields_to_order extends Migration
{
    public function up()
    {
        $this->addColumn('order', 'accrual_cash_back', $this->decimal(8, 2)->defaultValue(0)->comment('Начисление кэш-бэка')->after('price'));
        $this->addColumn('order', 'used_cash_back', $this->decimal(8, 2)->defaultValue(0)->comment('Использованный кэш-бэк для оплаты заказа')->after('accrual_cash_back'));
        $this->addColumn('order', 'penalty_cash_back', $this->decimal(8, 2)->defaultValue(0)->comment('Списанный кэш-бэк как штраф')->after('used_cash_back'));
    }

    public function down()
    {
        $this->dropColumn('order', 'accrual_cash_back');
        $this->dropColumn('order', 'penalty_cash_back');
        $this->dropColumn('order', 'used_cash_back');
    }
}
