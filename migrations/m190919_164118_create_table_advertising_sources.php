<?php

use yii\db\Migration;

/**
 * Class m190919_164118_create_table_advertising_sources
 */
class m190919_164118_create_table_advertising_sources extends Migration
{
    public function up()
    {
        $this->createTable('advertising_source', [
            'id' => $this->primaryKey(),
            'name' => $this->string(50)->comment('Рекламный источник'),
        ]);
    }

    public function down()
    {
        $this->dropTable('advertising_source');
    }
}
