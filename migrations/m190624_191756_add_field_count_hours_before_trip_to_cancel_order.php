<?php

use yii\db\Migration;

/**
 * Class m190624_191756_add_field_count_hours_before_trip_to_cancel_order
 */
class m190624_191756_add_field_count_hours_before_trip_to_cancel_order extends Migration
{
    public function up()
    {
        $this->addColumn('setting', 'count_hours_before_trip_to_cancel_order', $this->smallInteger()->defaultValue(3)->comment('Количество часов до первой точки рейса, меньше которых запрещено отменять заказ'));
    }

    public function down()
    {
        $this->dropColumn('setting', 'count_hours_before_trip_to_cancel_order');
    }
}
