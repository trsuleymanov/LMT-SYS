<?php

use yii\db\Migration;

class m170903_163121_create_table_dispatcher_accounting extends Migration
{
    public function up()
    {
//    - id
//    - operation_type  -> слово, список храниться в модели
//    - dispetcher_id  (user_id)
//    - created_at
//    - order_id  - значение или null

        $this->createTable('dispatcher_accounting', [ // лог действий операторов
            'id' => $this->primaryKey(),
            'operation_type' => $this->string(30)->comment('Тип операции'),
            'dispetcher_id' => $this->integer()->comment('Оператор (пользователь) совершивший действие'),
            'created_at' => $this->integer()->comment('Время совершения действия'),
            'order_id' => $this->integer()->comment('id Заказа'),
        ]);
    }

    public function down()
    {
        $this->dropTable('dispatcher_accounting');
    }
}
