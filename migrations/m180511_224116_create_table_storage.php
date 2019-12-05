<?php

use yii\db\Migration;

/**
 * Создание таблицы "Склад"
 */
class m180511_224116_create_table_storage extends Migration
{
    public function up()
    {
        $this->createTable('storage', [
            'id' => $this->primaryKey(),
            'name' => $this->string(50)->comment('Название'),
        ]);
    }

    public function down()
    {
        $this->dropTable('storage');
    }
}
