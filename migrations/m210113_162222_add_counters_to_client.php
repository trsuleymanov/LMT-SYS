<?php

use yii\db\Migration;

/**
 * Class m210113_162222_add_counters_to_client
 */
class m210113_162222_add_counters_to_client extends Migration
{
    public function up()
    {
        $this->addColumn('client', 'current_year_sended_standart_places', $this->smallInteger(2)->defaultValue(0)->comment('Число отправленных мест на стандартных рейсах')->after('current_year_sended_orders'));
        $this->addColumn('client', 'current_year_sended_standart_orders', $this->smallInteger(2)->defaultValue(0)->comment('Число отправленных заказов на стандарсных рейсах')->after('current_year_sended_standart_places'));
        $this->addColumn('client', 'current_year_sended_commercial_places', $this->smallInteger(2)->defaultValue(0)->comment('Число отправленных мест на коммерческих рейсах')->after('current_year_sended_standart_orders'));
        $this->addColumn('client', 'current_year_sended_commercial_orders', $this->smallInteger(2)->defaultValue(0)->comment('Число отправленных заказов на коммерческих рейсах')->after('current_year_sended_commercial_places'));
        $this->addColumn('client', 'current_year_sended_113_places', $this->smallInteger(2)->defaultValue(0)->comment('Число отправленных мест всего с 1 по 13 января включительно')->after('current_year_sended_commercial_orders'));
        $this->addColumn('client', 'current_year_sended_113_orders', $this->smallInteger(2)->defaultValue(0)->comment('Число отправленных заказов всего с 1 по 13 января включительно')->after('current_year_sended_113_places'));

        $this->createIndex('current_year_sended_standart_places', 'client', 'current_year_sended_standart_places');
        $this->createIndex('current_year_sended_standart_orders', 'client', 'current_year_sended_standart_orders');
        $this->createIndex('current_year_sended_commercial_places', 'client', 'current_year_sended_commercial_places');
        $this->createIndex('current_year_sended_commercial_orders', 'client', 'current_year_sended_commercial_orders');
        $this->createIndex('current_year_sended_113_places', 'client', 'current_year_sended_113_places');
        $this->createIndex('current_year_sended_113_orders', 'client', 'current_year_sended_113_orders');
    }

    public function down()
    {
        $this->dropColumn('client', 'current_year_sended_standart_places');
        $this->dropColumn('client', 'current_year_sended_standart_orders');
        $this->dropColumn('client', 'current_year_sended_commercial_places');
        $this->dropColumn('client', 'current_year_sended_commercial_orders');
        $this->dropColumn('client', 'current_year_sended_113_places');
        $this->dropColumn('client', 'current_year_sended_113_orders');

        $this->dropIndex('current_year_sended_standart_places', 'client');
        $this->dropIndex('current_year_sended_standart_orders', 'client');
        $this->dropIndex('current_year_sended_commercial_places', 'client');
        $this->dropIndex('current_year_sended_commercial_orders', 'client');
        $this->dropIndex('current_year_sended_113_places', 'client');
        $this->dropIndex('current_year_sended_113_orders', 'client');
    }
}
