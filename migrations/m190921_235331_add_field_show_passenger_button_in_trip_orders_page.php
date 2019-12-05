<?php

use yii\db\Migration;

/**
 * Class m190921_235331_add_field_show_passenger_button_in_trip_orders_page
 */
class m190921_235331_add_field_show_passenger_button_in_trip_orders_page extends Migration
{
    public function up()
    {
        $this->addColumn('setting', 'show_passenger_button_in_trip_orders_page', $this->boolean()->defaultValue(1)->comment('Показывать кнопку редактирования пассажиров на странице Состава рейса')->after('use_mobile_app_by_default'));
    }

    public function down()
    {
        $this->dropColumn('setting', 'show_passenger_button_in_trip_orders_page');
    }
}
