<?php

use yii\db\Migration;

/**
 * Создание таблицы "Состояние запчасти" - Новая, б/у, восстановленная, ремонтная и так далее
 */
class m180511_215110_create_table_transport_detail_state extends Migration
{
    public function up()
    {
        $this->createTable('transport_detail_state', [
            'id' => $this->primaryKey(),
            'name' => $this->string(30)->comment('Состояние'),
        ]);

        $aStates = [
            ['новая'],
            ['б/у'],
            ['восстановленная'],
            ['ремонтная'],
        ];
        $this->BatchInsert('transport_detail_state', ['name'], $aStates);

    }

    public function down()
    {
        $this->dropTable('transport_detail_state');
    }
}
