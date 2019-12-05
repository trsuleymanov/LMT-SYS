<?php

use yii\db\Migration;

/**
 * Создание таблицы "модели т/с"
 */
class m180511_214321_create_table_transport_model extends Migration
{
    public function up()
    {
        // Поля: ID, Наименование модели, Сокращенное наименование модели
        $this->createTable('transport_model', [
            'id' => $this->primaryKey(),
            'name' => $this->string(50)->comment('Наименование'),
            'sh_name' => $this->string(10)->comment('Сокращенное наименование'),
        ]);
    }

    public function down()
    {
        $this->dropTable('transport_model');
    }
}
