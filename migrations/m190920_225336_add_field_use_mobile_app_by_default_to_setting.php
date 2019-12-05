<?php

use yii\db\Migration;

/**
 * Class m190920_225336_add_field_use_mobile_app_by_default_to_setting
 */
class m190920_225336_add_field_use_mobile_app_by_default_to_setting extends Migration
{
    public function up()
    {
        $this->addColumn('setting', 'use_mobile_app_by_default', $this->boolean()->defaultValue(false)->comment('Использовать интерактивный режим отправки рейса по умолчанию')->after('count_hours_before_trip_to_cancel_order'));
    }

    public function down()
    {
        $this->dropColumn('setting', 'use_mobile_app_by_default');
    }
}
