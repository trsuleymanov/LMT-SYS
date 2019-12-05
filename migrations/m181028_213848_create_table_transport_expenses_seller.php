<?php

use yii\db\Migration;

/**
 * Class m181028_213848_create_table_transport_expenses_seller
 */
class m181028_213848_create_table_transport_expenses_seller extends Migration
{
    public function up()
    {
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

    public function down()
    {
        $this->dropTable('transport_expenses_seller');
    }
}
