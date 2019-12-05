<?php

use yii\db\Migration;

/**
 * Создание таблицы "операции на складе"
 */
class m180511_232455_create_table_storage_operation extends Migration
{
    public function up()
    {
//        ID
//        ID запчасти из таблицы Склад
//        Количество в штуках (Сколько выдано, например, литров масла)
//        C какой машиной связана операция
//        С каким водителем связана операция
//        Дата операции
//        Тип операции
//        Комментарий

        $this->createTable('storage_operation', [
            'id' => $this->primaryKey(),
            'storage_detail_id' => $this->integer()->comment('Запчасть на складе'),
            'count' => $this->smallInteger()->comment('Количество'),
            'transport_id' => $this->integer()->comment('C какой машиной связана операция'),
            'driver_id' => $this->integer()->comment('С каким водителем связана операция'),
            'created_at' => $this->integer()->comment('Дата операция'),
            'operation_type_id' => $this->integer()->comment('Тип операции'),
            'comment' => $this->text()->comment('Комментарий'),
        ]);
    }

    public function down()
    {
        $this->dropTable('storage_operation');
    }
}
