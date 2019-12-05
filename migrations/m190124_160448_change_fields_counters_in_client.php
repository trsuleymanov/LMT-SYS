<?php

use yii\db\Migration;

/**
 * Class m190124_160448_change_fields_counters_in_client
 */
class m190124_160448_change_fields_counters_in_client extends Migration
{
    public function up()
    {
        $this->dropColumn('client', 'current_year_places_reliability');
        $this->dropColumn('client', 'current_year_orders_reliability');

        $this->addColumn('client', 'current_year_canceled_orders_1h',  $this->integer()->comment('В текущем году: количество отмененных заказов менее чем за 1 час до первой точки рейса или на протяжении 3 часов после рейса')->after('current_year_canceled_orders'));
        $this->addColumn('client', 'current_year_canceled_orders_12h',  $this->integer()->comment('В текущем году: количество отмененных заказов менее чем за 12 часов до последней точки рейса')->after('current_year_canceled_orders_1h'));

        $this->addColumn('client', 'past_years_canceled_orders_1h',  $this->integer()->comment('За прошлые годы: количество отмененных заказов менее чем за 1 час до первой точки рейса или на протяжении 3 часов после рейса')->after('past_years_canceled_orders'));
        $this->addColumn('client', 'past_years_canceled_orders_12h',  $this->integer()->comment('За прошлые годы: количество отмененных заказов менее чем за 12 часов до последней точки рейса')->after('past_years_canceled_orders_1h'));
    }

    public function down()
    {
        $this->addColumn('client', 'current_year_places_reliability', $this->decimal(8, 2)->defaultValue(0)->comment('Надежность по местам в текущем году')->after('current_year_canceled_orders'));
        $this->addColumn('client', 'current_year_orders_reliability', $this->decimal(8, 2)->defaultValue(0)->comment('Надежность по заказам в текущем году')->after('current_year_places_reliability'));

        $this->dropColumn('client', 'current_year_canceled_orders_1h');
        $this->dropColumn('client', 'current_year_canceled_orders_12h');

        $this->dropColumn('client', 'past_years_canceled_orders_1h');
        $this->dropColumn('client', 'past_years_canceled_orders_12h');
    }
}
