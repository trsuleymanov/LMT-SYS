<?php

use yii\db\Migration;

/**
 * Class m190127_203348_create_table_call_appeal
 */
class m190127_203348_create_table_call_appeal extends Migration
{
    public function up()
    {
        $this->createTable('call_appeal', [
            'id' => $this->primaryKey(),
            'created_at' => $this->integer()->comment('Время создания'),
            'type' => "ENUM('administrative_request', 'information_request', 'operation_with_order')",
            'contact_id' => $this->integer()->comment('Контакт'),
            'success_call_id' => $this->integer()->comment('Звонок, инициировавший обращение'),
            'client_phone' => $this->string(20)->comment('Телефон клиента'),
            'operator_user_id' => $this->integer()->comment('Оператор, принявший звонок'),
        ]);

        $this->addColumn('dispatcher_accounting', 'call_appeal_id', $this->integer()->after('id')->comment('Обращение'));
    }

    public function down()
    {
        $this->dropTable('call_appeal');

        $this->dropColumn('dispatcher_accounting', 'call_appeal_id');
    }
}
