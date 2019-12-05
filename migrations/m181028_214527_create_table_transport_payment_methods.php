<?php

use yii\db\Migration;

/**
 * Class m181028_214527_create_table_transport_payment_methods
 */
class m181028_214527_create_table_transport_payment_methods extends Migration
{
    public function up()
    {
        $this->createTable('transport_payment_methods', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->comment('Наименование'),
        ]);

        $this->BatchInsert('transport_payment_methods',
            ['name',],
            [
                ['Из выручки'],
                ['Безналично'],
                ['Перевод на карту'],
                ['Оплата наличными'],
            ]
        );
    }

    public function down()
    {
        $this->dropTable('transport_payment_methods');
    }
}
