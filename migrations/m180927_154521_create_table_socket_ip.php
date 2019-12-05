<?php

use yii\db\Migration;

/**
 * Class m180927_154521_create_table_socket_ip
 */
class m180927_154521_create_table_socket_ip extends Migration
{
    public function up()
    {
        $this->createTable('socket_ip', [
            'id' => $this->primaryKey(),
            'ip' => $this->string(22)->comment('ip адрес сервера'),
            'description' => $this->string(255)->comment('описание'),
        ]);
    }

    public function down()
    {
        $this->dropTable('socket_ip');
    }
}
