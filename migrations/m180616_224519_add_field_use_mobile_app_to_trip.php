<?php

use yii\db\Migration;

/**
 * Class m180616_224519_add_field_use_mobile_app_to_trip
 */
class m180616_224519_add_field_use_mobile_app_to_trip extends Migration
{
    public function up()
    {
        $this->addColumn('trip', 'use_mobile_app', $this->boolean()->after('sended_user_id')->comment('Режим работы рейса: 0 - без водительского приложения, 1 - с водительским приложением'));
    }

    public function down()
    {
        $this->dropColumn('trip', 'use_mobile_app');
    }
}
