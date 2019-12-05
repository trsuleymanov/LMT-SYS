<?php

use yii\db\Migration;


class m180621_143318_create_table_measurement_value extends Migration
{
    public function up()
    {
        $this->createTable('detail_measurement_value', [
            'id' => $this->primaryKey(),
            'name' => $this->string(30)->comment('Название'),
        ]);

        $aValues = [
            ['шт.'],
            ['л'],
            ['комплектов'],
        ];
        $this->BatchInsert('detail_measurement_value', ['name'], $aValues);

    }

    public function down()
    {
        $this->dropTable('detail_measurement_value');
    }
}
