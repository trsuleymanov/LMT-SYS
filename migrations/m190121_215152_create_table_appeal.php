<?php

use yii\db\Migration;

/**
 * Class m190121_215152_create_table_appeal
 */
class m190121_215152_create_table_appeal extends Migration
{
    public function up()
    {
        $this->createTable('call_appeal', [
            'id' => $this->primaryKey(),
            'created_at' => $this->integer()->comment('Время создания'),
            'client_phone' => $this->string(20)->comment('Телефон клиента'),
            'completed_at' => $this->integer()->comment('Время завершения'),
        ]);

        $this->addColumn('call', 'call_appeal_id', $this->integer()->after('id')->comment('Обращение'));
    }

    public function down()
    {
        $this->dropTable('call_appeal');

        $this->dropColumn('call', 'call_appeal_id');
    }
}
