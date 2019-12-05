<?php

use yii\db\Migration;

class m171014_080208_add_field_sender_id_to_trip_transport extends Migration
{
    public function up()
    {
        $this->addColumn('trip_transport', 'sender_id', $this->integer()->comment('Пользователь отправивший т/с')->after('date_sended'));
    }

    public function down()
    {
        $this->dropColumn('trip_transport', 'sender_id');
    }
}
