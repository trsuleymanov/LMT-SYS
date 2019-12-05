<?php

use yii\db\Migration;

/**
 * Class m190919_165247_create_table_advertising_source_report
 */
class m190919_165247_create_table_advertising_source_report extends Migration
{
    public function up()
    {
        $this->createTable('advertising_source_report', [
            'id' => $this->primaryKey(),
            'advertising_source_id' => $this->integer()->comment('Рекламный источник'),
            'phone' => $this->string(20)->comment('Телефон'),
            'client_id' => $this->integer()->comment('Клиент'),
            'created_at' => $this->integer()->comment('Время создания'),
        ]);
    }

    public function down()
    {
        $this->dropTable('advertising_source_report');
    }
}
