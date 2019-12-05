<?php

use yii\db\Migration;

/**
 * Class m180927_175038_add_field_socket_ip_id_to_user
 */
class m180927_175038_add_field_socket_ip_id_to_user extends Migration
{
    public function up()
    {
        $this->addColumn('user', 'socket_ip_id', $this->integer()->after('lat_long_ping_at')->comment('сокет ip-адрес по которому браузер соединяется с сервером'));
    }

    public function down()
    {
        $this->dropColumn('user', 'socket_ip_id');
    }
}
