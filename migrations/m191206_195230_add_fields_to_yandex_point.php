<?php

use yii\db\Migration;

/**
 * Class m191206_195230_add_fields_to_yandex_point
 */
class m191206_195230_add_fields_to_yandex_point extends Migration
{
    public function up()
    {
        $this->addColumn('yandex_point', 'popular_departure_point', $this->boolean()->defaultValue(false)->after('super_tariff_used')->comment('Популярная точка отправления'));
        $this->addColumn('yandex_point', 'popular_arrival_point', $this->boolean()->defaultValue(false)->after('popular_departure_point')->comment('Популярная точка прибытия'));
    }

    public function down()
    {
        $this->dropColumn('yandex_point', 'popular_arrival_point');
        $this->dropColumn('yandex_point', 'popular_departure_point');
    }
}
