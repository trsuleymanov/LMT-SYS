<?php

use yii\db\Migration;

class m180121_012843_create_table_client_ext extends Migration
{
    public function up()
    {
        $this->createTable('client_ext', [  // заявки-заказы клиентов
            'id' => $this->primaryKey(),
            'client_server_ext_id' => $this->integer()->comment('id заявки на клиентском сервере'),
            'status' => $this->smallInteger()->comment('Статус'),
            'direction_id' => $this->integer()->comment('Направление'),
            'data_mktime' => $this->integer()->comment('Дата'),
            'time' => $this->string(5)->comment('Время'),
            'client_id' => $this->integer()->comment('Клиента id'),
            'client_fio' => $this->string(100)->comment('Клиента фамилия'),
            'order_id' => $this->integer()->comment('Заказ'),
            'created_at' => $this->integer()->comment('Время создания'),
            'updated_at' => $this->integer()->comment('Время изменения'),
        ]);
    }

    public function down()
    {
        $this->dropTable('client_ext');
    }
}
