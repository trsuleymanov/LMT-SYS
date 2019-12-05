<?php

use yii\db\Migration;

/**
 * Class m190521_234303_create_table_trip_operation
 */
class m190521_234303_create_table_trip_operation extends Migration
{
    public function up()
    {
        $this->createTable('trip_operation', [
            'id' => $this->primaryKey(),
            'created_at' => $this->integer()->comment('Время проведения операции'),

            // здесь надо придумать как можно было бы сохранять источник-рейс/рейсы, и результирующий
            // рейс/рейсы без создания доп.таблицы. И еще помимо id рейсов сохранять имена рейсов нужно!

            'user_id' => $this->integer()->comment('Кем произведена операция'),
            'type' => "ENUM('create', 'update', 'merge', 'set_commercial', 'unset_commercial', 'start_send', 'send', 'cancel_send')",
            'comment' => $this->string(255),
            'delta' => $this->integer()->comment('Разница во времени начальной точки между рейсами при редактировании или слиянии рейсов'),
        ]);
    }

    public function down()
    {
        $this->dropTable('trip_operation');
    }
}
