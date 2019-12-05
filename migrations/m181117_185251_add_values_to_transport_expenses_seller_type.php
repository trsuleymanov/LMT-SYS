<?php

use yii\db\Migration;

/**
 * Class m181117_185251_add_values_to_transport_expenses_seller_type
 */
class m181117_185251_add_values_to_transport_expenses_seller_type extends Migration
{
    public function up()
    {
        $this->truncateTable('transport_expenses_seller_type');
        $this->truncateTable('transport_expenses');

        $aSelleTypes = [
            ['Введите название'],
            ['АЗС'],
            ['Мойка'],
            ['Стоянка'],
            ['Оформление документов'],
            ['Прочие платежи'],
            ['Покупка ЗЧ'],
            ['Автосервис'],
            ['Омыватель стекла'],
            ['Лампочки'],
        ];

        $this->BatchInsert('transport_expenses_seller_type', ['name', ], $aSelleTypes);
    }

    public function down()
    {
    }

}
