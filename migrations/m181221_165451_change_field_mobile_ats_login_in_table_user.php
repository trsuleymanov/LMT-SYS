<?php

use yii\db\Migration;

/**
 * Class m181221_165451_change_field_mobile_ats_login_in_table_user
 */
class m181221_165451_change_field_mobile_ats_login_in_table_user extends Migration
{
    public function up()
    {
        $this->alterColumn('user', 'mobile_ats_login', $this->string(100)->after('username')->comment('Логин в АТС'));
    }

    public function down()
    {
        $this->alterColumn('user', 'mobile_ats_login', $this->string(30)->after('username')->comment('Логин в АТС'));
    }
}
