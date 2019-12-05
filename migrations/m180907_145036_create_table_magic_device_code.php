<?php

use yii\db\Migration;


class m180907_145036_create_table_magic_device_code extends Migration
{
    public function up()
    {
        $this->createTable('magic_device_code', [
            'id' => $this->primaryKey(),
            'code' => $this->string(17)->comment('Код устройства')->unique(),
        ]);
    }

    public function down()
    {
        $this->dropTable('magic_device_code');
    }
}
