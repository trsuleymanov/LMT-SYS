<?php

use yii\db\Migration;

class m180921_191639_add_mobile_ats_login_field_to_user extends Migration
{
    public function up()
    {
        $this->addColumn('user', 'mobile_ats_login', $this->string(30)->after('username')->comment('Логин в мобильной АТС'));
    }

    public function down()
    {
        $this->dropColumn('user', 'mobile_ats_login');
    }
}
