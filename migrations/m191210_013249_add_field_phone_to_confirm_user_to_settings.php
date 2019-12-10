<?php

use yii\db\Migration;

/**
 * Class m191210_013249_add_field_phone_to_confirm_user_to_settings
 */
class m191210_013249_add_field_phone_to_confirm_user_to_settings extends Migration
{
    public function up()
    {
        $this->addColumn('setting', 'phone_to_confirm_user', $this->string(20)->defaultValue('4002')->comment('Телефон в АТС куда переадресуется звонок для подтверждения пользователя во время регистрации')->after('show_passenger_button_in_trip_orders_page'));
    }

    public function down()
    {
        $this->dropColumn('setting', 'phone_to_confirm_user');
    }
}
