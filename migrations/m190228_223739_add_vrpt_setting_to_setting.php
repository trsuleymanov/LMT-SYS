<?php

use yii\db\Migration;

/**
 * Class m190228_223739_add_vrpt_setting_to_setting
 */
class m190228_223739_add_vrpt_setting_to_setting extends Migration
{
    public function up()
    {
        $this->addColumn('setting', 'ya_point_p_AK', $this->integer()->defaultValue(0)->comment('для АК: Минимальное количество точек на рейс, меньше которого точки рейса не учитываются'));
        $this->addColumn('setting', 'ya_point_p_KA', $this->integer()->defaultValue(0)->comment('для КА: Минимальное количество точек на рейс, меньше которого точки рейса не учитываются'));
        $this->addColumn('setting', 'max_time_short_trip_AK', $this->integer()->defaultValue(2400)->comment('Максимальное время короткого сбора для АК'));
        $this->addColumn('setting', 'max_time_short_trip_KA', $this->integer()->defaultValue(2400)->comment('Максимальное время короткого сбора для КА'));
    }

    public function down()
    {
        $this->dropColumn('setting', 'ya_point_p_AK');
        $this->dropColumn('setting', 'ya_point_p_KA');
        $this->dropColumn('setting', 'max_time_short_trip_AK');
        $this->dropColumn('setting', 'max_time_short_trip_KA');
    }
}
