<?php

use yii\db\Migration;

/**
 * Class m181104_000837_create_table_transport_expenses_detailing
 */
class m181104_000837_create_table_transport_expenses_detailing extends Migration
{
    public function up()
    {
//        - id,
//        - id_расходов
//        - наименование документа (этот самый номер),
//        - дата документа (эта самая дата),
//        - наименование,
//        - сумма,
//        - тип (это или работа, или запчасть, или деталь)
        $this->createTable('transport_expenses_detailing', [
            'id' => $this->primaryKey(),
            'expense_id' => $this->integer()->comment('Расход'),
            //'doc_name' => $this->string(100)->comment('Наименование документа'),
            //'doc_date' => $this->integer()->comment('Дата документа'),
            'name' => $this->string(100)->comment('Наименование'),
            'price' => $this->decimal(8, 2)->defaultValue(0)->comment('Сумма, руб'),
            'type' => "ENUM('work', 'spare_part', 'detail')"
        ]);
    }

    public function down()
    {
        $this->dropTable('transport_expenses_detailing');
    }
}
