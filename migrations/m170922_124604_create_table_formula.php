<?php

use yii\db\Migration;

class m170922_124604_create_table_formula extends Migration
{
    public function up()
    {
        $this->createTable('formula', [ // список формул
            'id' => $this->primaryKey(),
            'name' => $this->string(50)->comment('Название'),
            'formula' => $this->string(255)->comment('Формула'),
            'created_at' => $this->integer()->comment('Время создания формулы'),
            'updated_at' => $this->integer()->comment('Время изменения формулы'),
        ]);
    }

    public function down()
    {
        $this->dropTable('formula');
    }
}
