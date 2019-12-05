<?php

use yii\db\Migration;

/**
 * Class m181215_001109_add_counts_indexes_to_table_client
 */
class m181215_001109_add_counts_indexes_to_table_client extends Migration
{
    public function up()
    {
        $this->createIndex('current_year_sended_places', 'client', 'current_year_sended_places');
        $this->createIndex('current_year_sended_orders', 'client', 'current_year_sended_orders');
        $this->createIndex('current_year_canceled_places', 'client', 'current_year_canceled_places');
        $this->createIndex('current_year_canceled_orders', 'client', 'current_year_canceled_orders');
        $this->createIndex('current_year_places_reliability', 'client', 'current_year_places_reliability');
        $this->createIndex('current_year_orders_reliability', 'client', 'current_year_orders_reliability');

        $this->createIndex('current_year_sended_prize_places', 'client', 'current_year_sended_prize_places');
        $this->createIndex('current_year_penalty', 'client', 'current_year_penalty');
        $this->createIndex('current_year_sended_fixprice_places', 'client', 'current_year_sended_fixprice_places');
        $this->createIndex('current_year_sended_fixprice_orders', 'client', 'current_year_sended_fixprice_orders');
        $this->createIndex('current_year_sended_informer_beznal_places', 'client', 'current_year_sended_informer_beznal_places');
        $this->createIndex('current_year_sended_informer_beznal_orders', 'client', 'current_year_sended_informer_beznal_orders');
        $this->createIndex('current_year_sended_isnotplaces_orders', 'client', 'current_year_sended_isnotplaces_orders');

        $this->createIndex('past_years_sended_places', 'client', 'past_years_sended_places');
        $this->createIndex('past_years_sended_orders', 'client', 'past_years_sended_orders');
        $this->createIndex('past_years_canceled_places', 'client', 'past_years_canceled_places');
        $this->createIndex('past_years_canceled_orders', 'client', 'past_years_canceled_orders');
        $this->createIndex('past_years_sended_prize_places', 'client', 'past_years_sended_prize_places');

        $this->createIndex('past_years_penalty', 'client', 'past_years_penalty');
        $this->createIndex('past_years_sended_fixprice_places', 'client', 'past_years_sended_fixprice_places');
        $this->createIndex('past_years_sended_fixprice_orders', 'client', 'past_years_sended_fixprice_orders');
        $this->createIndex('past_years_sended_informer_beznal_places', 'client', 'past_years_sended_informer_beznal_places');
        $this->createIndex('past_years_sended_informer_beznal_orders', 'client', 'past_years_sended_informer_beznal_orders');
        $this->createIndex('past_years_sended_isnotplaces_orders', 'client', 'past_years_sended_isnotplaces_orders');
    }

    public function down()
    {
//        current_year_sended_places
//        current_year_sended_orders
//        current_year_canceled_places
//        current_year_canceled_orders
//        current_year_places_reliability
//        current_year_orders_reliability

//        current_year_sended_prize_places
//        current_year_penalty
//        current_year_sended_fixprice_places
//        current_year_sended_fixprice_orders
//        current_year_sended_informer_beznal_places
//        current_year_sended_informer_beznal_orders
//        current_year_sended_isnotplaces_orders
//
//        past_years_sended_places
//        past_years_sended_orders
//        past_years_canceled_places
//        past_years_canceled_orders
//        past_years_sended_prize_places

//        past_years_penalty
//        past_years_sended_fixprice_places
//        past_years_sended_fixprice_orders
//        past_years_sended_informer_beznal_places
//        past_years_sended_informer_beznal_orders
//        past_years_sended_isnotplaces_orders

        $this->dropIndex('current_year_sended_places', 'client');
        $this->dropIndex('current_year_sended_orders', 'client');
        $this->dropIndex('current_year_canceled_places', 'client');
        $this->dropIndex('current_year_canceled_orders', 'client');
        $this->dropIndex('current_year_places_reliability', 'client');
        $this->dropIndex('current_year_orders_reliability', 'client');

        $this->dropIndex('current_year_sended_prize_places', 'client');
        $this->dropIndex('current_year_penalty', 'client');
        $this->dropIndex('current_year_sended_fixprice_places', 'client');
        $this->dropIndex('current_year_sended_fixprice_orders', 'client');
        $this->dropIndex('current_year_sended_informer_beznal_places', 'client');
        $this->dropIndex('current_year_sended_informer_beznal_orders', 'client');
        $this->dropIndex('current_year_sended_isnotplaces_orders', 'client');

        $this->dropIndex('past_years_sended_places', 'client');
        $this->dropIndex('past_years_sended_orders', 'client');
        $this->dropIndex('past_years_canceled_places', 'client');
        $this->dropIndex('past_years_canceled_orders', 'client');
        $this->dropIndex('past_years_sended_prize_places', 'client');

        $this->dropIndex('past_years_penalty', 'client');
        $this->dropIndex('past_years_sended_fixprice_places', 'client');
        $this->dropIndex('past_years_sended_fixprice_orders', 'client');
        $this->dropIndex('past_years_sended_informer_beznal_places', 'client');
        $this->dropIndex('past_years_sended_informer_beznal_orders', 'client');
        $this->dropIndex('past_years_sended_isnotplaces_orders', 'client');
    }
}
