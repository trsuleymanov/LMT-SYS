<?php

use yii\db\Migration;

/**
 * Создание таблицы "Происхождение"
 */
class m180512_100158_create_table_transport_detail_origin extends Migration
{
    public function up()
    {
        $this->createTable('transport_detail_origin', [
            'id' => $this->primaryKey(),
            'name' => $this->string(50)->comment('Происхождение'),
        ]);

        $aOrigins = [
            ['оригинальная'],
            ['неоригинальная'],
        ];
        $this->BatchInsert('transport_detail_origin', ['name'], $aOrigins);
    }

    public function down()
    {
        $this->dropTable('transport_detail_origin');
    }
}
