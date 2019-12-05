<?php

use yii\db\Migration;


class m180208_132006_add_fields_to_clientext extends Migration
{
    public function up()
    {
        $this->addColumn('client_ext', 'client_phone', $this->string(20)->comment('Телефон клиента')->after('client_fio'));
        $this->addColumn('client_ext', 'client_email', $this->string(50)->comment('Эл.почта клиента')->after('client_phone'));
    }

    public function down()
    {
        $this->dropColumn('client_ext', 'client_phone');
        $this->dropColumn('client_ext', 'client_email');
    }
}
