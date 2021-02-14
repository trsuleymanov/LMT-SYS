<?php

use yii\db\Migration;

/**
 * Class m210212_062301_add_price_diff_fields_to_yandex_point
 */
class m210212_062301_add_price_diff_fields_to_yandex_point extends Migration
{
    public function up()
    {
        $this->addColumn('yandex_point', 'point_from_standart_price_diff', $this->integer()->comment('Стандартная наценка/скидка на точке посадки')->defaultValue(0)->after('time_to_get_together_long'));
        $this->addColumn('yandex_point', 'point_from_commercial_price_diff', $this->integer()->comment('На коммерческом рейсе наценка/скидка на точке посадки')->defaultValue(0)->after('point_from_standart_price_diff'));
        $this->addColumn('yandex_point', 'point_to_standart_price_diff', $this->integer()->comment('Стандартная наценка/скидка на точке высадки')->defaultValue(0)->after('point_from_commercial_price_diff'));
        $this->addColumn('yandex_point', 'point_to_commercial_price_diff', $this->integer()->comment('На коммерческом рейсе наценка/скидка на точке высадки')->defaultValue(0)->after('point_to_standart_price_diff'));
    }

    public function down()
    {
        $this->dropColumn('yandex_point', 'point_from_standart_price_diff');
        $this->dropColumn('yandex_point', 'point_from_commercial_price_diff');
        $this->dropColumn('yandex_point', 'point_to_standart_price_diff');
        $this->dropColumn('yandex_point', 'point_to_commercial_price_diff');
    }
}
