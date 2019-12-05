<?php

use yii\db\Migration;

/**
 * Создание таблицы «Типы операций»
 */
class m180511_231114_create_table_storage_operation_type extends Migration
{
    public function up()
    {
//        ID,
//        наименование операции,
//        операция ли это прихода или операция расхода, то есть плюсовать или минусовать

        $this->createTable('storage_operation_type', [
            'id' => $this->primaryKey(),
            'name' => $this->string(50)->comment('Наименование операции'),
            'operation_type' => $this->boolean()->defaultValue(0)->comment('Тип операции: 0 - расход, 1 - приход'),
        ]);
    }

    public function down()
    {
        $this->dropTable('storage_operation_type');
    }
}
