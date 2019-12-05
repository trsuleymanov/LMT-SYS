<?php

use yii\db\Migration;

class m170907_222727_change_user_table extends Migration
{
    public function up()
    {
        $this->addColumn('user', 'token', $this->string(255)->comment('Токен устройства')->after('password_hash'));
    }

    public function down()
    {
        $this->dropColumn('user', 'token');
    }
}
