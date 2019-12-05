<?php

use yii\db\Migration;

class m171010_211954_rename_some_counts_in_table_clients extends Migration
{
    public function up()
    {
//        sended_order_count -> sended_orders_places_count
//        sended_fixed_price_order_count -> sended_fixprice_orders_places_count
//        canceled_order_count -> canceled_orders_places_count

        $this->alterColumn('client', 'sended_order_count', $this->smallInteger(2)->defaultValue(0)->comment('Количество мест в отправленных заказах'));
        $this->renameColumn('client', 'sended_order_count', 'sended_orders_places_count');

        $this->alterColumn('client', 'sended_fixed_price_order_count', $this->smallInteger(2)->defaultValue(0)->comment('Количество мест в отправленных заказах с фиксированной ценой'));
        $this->renameColumn('client', 'sended_fixed_price_order_count', 'sended_fixprice_orders_places_count');

        $this->alterColumn('client', 'canceled_order_count', $this->smallInteger(2)->defaultValue(0)->comment('Количество мест в отмененных заказах'));
        $this->renameColumn('client', 'canceled_order_count', 'canceled_orders_places_count');
    }

    public function down()
    {
        $this->alterColumn('client', 'sended_orders_places_count', $this->smallInteger(2)->defaultValue(0)->comment('Количество отправленных заказов'));
        $this->renameColumn('client', 'sended_orders_places_count', 'sended_order_count');

        $this->alterColumn('client', 'sended_fixprice_orders_places_count', $this->smallInteger(2)->defaultValue(0)->comment('Количество отправленных заказов с фиксированной ценой'));
        $this->renameColumn('client', 'sended_fixprice_orders_places_count', 'sended_fixed_price_order_count');

        $this->alterColumn('client', 'canceled_orders_places_count', $this->smallInteger(2)->defaultValue(0)->comment('Количество отмененных заказов'));
        $this->renameColumn('client', 'canceled_orders_places_count', 'canceled_order_count');
    }
}
