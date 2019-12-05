<?php

use yii\db\Migration;

class m171220_235542_add_field_access_key_to_trip_transport extends Migration
{
    public function up()
    {
        $this->addColumn('trip_transport', 'access_key', $this->string(10)->defaultValue('')->after('id')->comment('Ключ доступа для водителя в приложение'));
    }

    public function down()
    {
        $this->dropColumn('trip_transport', 'access_key');
    }
}
