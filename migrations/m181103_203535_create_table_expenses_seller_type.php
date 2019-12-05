<?php

use yii\db\Migration;

/**
 * Class m181103_203535_create_table_expenses_seller_type
 */
class m181103_203535_create_table_expenses_seller_type extends Migration
{
    public function up()
    {
        $this->dropTable('transport_expenses_seller');

        $this->createTable('transport_expenses_seller_type', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->comment('Наименование'),
        ]);

        $this->BatchInsert('transport_expenses_seller_type',
            ['name',],
            [
                ['АЗС'],
                ['Мойка'],
                ['Стоянка'],
                ['Прочие']
            ]
        );
    }

    public function down()
    {
        $this->dropTable('transport_expenses_seller_type');

        $this->createTable('transport_expenses_seller', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->comment('Наименование'),
        ]);

        $this->BatchInsert('transport_expenses_seller',
            ['name',],
            [
                ['АЗС'],
                ['Мойка'],
                ['Стоянка'],
                ['Прочие']
            ]
        );
    }
}
