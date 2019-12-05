<?php

use yii\db\Migration;

/**
 * Class m190126_155748_delete_table_call_appeal_create_table_call_contact
 */
class m190126_155748_delete_table_call_appeal_create_table_call_contact extends Migration
{
    public function up()
    {
        $this->dropTable('call_appeal');
        $this->createTable('call_contact', [
            'id' => $this->primaryKey(),
            'initiator' => "ENUM('client', 'operator')",
            'initiator_operator_user_id' => $this->integer()->comment('id оператора инициировшего звонок'),
            'created_at' => $this->integer()->comment('Время создания'),
            'client_phone' => $this->string(20)->comment('Телефон клиента'),
            'operator_user_id' => $this->integer()->comment('Оператор, поговоривший с клиентом'),
            'completed_at' => $this->integer()->comment('Время завершения'),
        ]);

        $this->dropColumn('call', 'call_appeal_id');
        $this->addColumn('call', 'call_contact_id', $this->integer()->after('id')->comment('Контакт'));
    }

    public function down()
    {
        $this->dropTable('call_contact');

        $this->createTable('call_appeal', [
            'id' => $this->primaryKey(),
            'created_at' => $this->integer()->comment('Время создания'),
            'client_phone' => $this->string(20)->comment('Телефон клиента'),
            'completed_at' => $this->integer()->comment('Время завершения'),
        ]);

        $this->dropColumn('call', 'call_contact_id');
        $this->addColumn('call', 'call_appeal_id', $this->integer()->after('id')->comment('Обращение'));
    }

}
