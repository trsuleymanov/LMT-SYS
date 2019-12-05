<?php

use yii\db\Migration;

class m171214_174409_add_auth_seans_finish_to_user extends Migration
{
    public function up()
    {
        $this->addColumn('user', 'auth_seans_finish', $this->integer()->comment('Время окончания сеанса пользователя')->after('auth_key'));
    }

    public function down()
    {
        $this->dropColumn('user', 'auth_seans_finish');
    }
}
