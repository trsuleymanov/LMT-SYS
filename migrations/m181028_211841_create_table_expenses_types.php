<?php

use yii\db\Migration;

/**
 * Class m181028_211841_create_table_expenses_types
 */
class m181028_211841_create_table_expenses_types extends Migration
{
    public function up()
    {
        $this->createTable('transport_expenses_types', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->comment('Наименование'),
        ]);

        $this->BatchInsert('transport_expenses_types',
            ['name',],
            [
                ['Фискальный чек'],
                ['Заказ-наряд'],
                ['Товарный чек'],
            ]
        );
    }

    public function down()
    {
        $this->dropTable('transport_expenses_types');
    }
}
