<?php

use yii\db\Migration;


class m180203_141741_add_field_email_to_client extends Migration
{
    public function up()
    {
        $this->addColumn('client', 'email', $this->string(50)->comment('Электронная почта')->after('id'));
    }

    public function down()
    {
        $this->dropColumn('client', 'email');
    }
}
