<?php

use yii\db\Migration;

/**
 * Class m210212_062301_add_price_diff_fields_to_yandex_point
 */
class m210212_062301_add_price_diff_fields_to_yandex_point extends Migration
{
    public function up()
    {
        $this->addColumn('yandex_point', 'standart_price_diff', $this->integer()->comment('Стандартная наценка/скидка на точке')->defaultValue(0)->after('time_to_get_together_long'));
        $this->addColumn('yandex_point', 'commercial_price_diff', $this->integer()->comment('На коммерческом рейсе наценка/скидка на точке')->defaultValue(0)->after('standart_price_diff'));
    }

    public function down()
    {
        $this->dropColumn('yandex_point', 'standart_price_diff');
        $this->dropColumn('yandex_point', 'commercial_price_diff');
    }
}
