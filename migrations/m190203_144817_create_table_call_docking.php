<?php

use yii\db\Migration;

/**
 * Class m190203_144817_create_table_call_docking
 */
class m190203_144817_create_table_call_docking extends Migration
{
    public function up()
    {
        $this->dropTable('call_contact');

        $this->createTable('call_docking', [
            'id' => $this->primaryKey(),
            'call_id' => $this->integer()->comment('Звонок'),
            'case_id' => $this->integer()->comment('Обращение'),
            'conformity' => $this->boolean()->defaultValue(0)->comment('Да/Нет - определяет соответствие номера операнда и ID клиента'),

            // Без действия/+Новый заказ/+ПДТ/КЗМ/+Посадка/+Высадка/+Редактирование/+Удаление
            //  +'order_create' => 'Первичная запись',
            //  +'order_update' => 'Редактирование заказа',
            //  +'order_confirm' => 'Подтверждение заказа',
            //  +'order_cancel' => 'Удаление заказа',
            //  +'order_sat_to_transport' => 'Посадка в машину',
            //  +'order_unsat_from_transport' => 'Высадка из машины',
            'click_event' => $this->string(30)->comment('Событие'),
        ]);
    }

    public function down()
    {
        $this->dropTable('call_docking');

        $this->createTable('call_contact', [
            'id' => $this->primaryKey(),
            'initiator' => "ENUM('client', 'operator')",
            'initiator_operator_user_id' => $this->integer()->comment('id оператора инициировшего звонок'),
            'created_at' => $this->integer()->comment('Время создания'),
            'client_phone' => $this->string(20)->comment('Телефон клиента'),
            'operator_user_id' => $this->integer()->comment('Оператор, поговоривший с клиентом'),
            'completed_at' => $this->integer()->comment('Время завершения'),
        ]);
    }
}
