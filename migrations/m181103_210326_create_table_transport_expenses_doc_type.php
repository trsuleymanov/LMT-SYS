<?php

use yii\db\Migration;

/**
 * Class m181103_210326_create_table_transport_expenses_doc_type
 */
class m181103_210326_create_table_transport_expenses_doc_type extends Migration
{
    public function up()
    {
        $this->createTable('transport_expenses_doc_type', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->comment('Наименование'),
        ]);

        $this->BatchInsert('transport_expenses_seller_type',
            ['name',],
            [
                ['Оригинал'],
                ['Неоригинал'],
                ['Отчет'],
                ['Авансовый платеж']
            ]
        );
    }

    public function down()
    {
        $this->dropTable('transport_expenses_doc_type');
    }
}
