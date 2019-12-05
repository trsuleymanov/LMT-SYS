<?php

use yii\db\Migration;

class m170827_002059_create_table_chat_message extends Migration
{
    public function up()
    {
        $this->createTable('chat_message', [
            'id' => $this->primaryKey(),
            'created_at' => $this->integer()->comment('Дата создания'),
            //'lifetime' => $this->integer()->comment('Время жизни'),
            'expiration_time' => $this->integer()->comment('Время истечения действия сообщения'),
            'to_the_begining' => $this->boolean()->defaultValue(0)->comment('Переместить в начало'),
            'message' => $this->string(255)->comment('Сообщение')
        ]);
    }

    public function down()
    {
        $this->dropTable('chat_message');
    }
}
